import pool from "../utils/database";
import { geoUtils } from "../utils/redis";
import { Kafka } from "kafkajs";
import dotenv from "dotenv";

dotenv.config();

interface LocationUpdate {
  driverId: string;
  latitude: number;
  longitude: number;
  heading?: number;
  speed?: number;
  accuracy?: number;
  status?: string;
}

interface RideProgress {
  progress: number;
  remainingDistance: number;
  remainingTime: number;
  currentLocation: {
    lat: number;
    lng: number;
  };
}

// Initialize Kafka producer for publishing events
let kafka: Kafka | null = null;
let producer: any = null;

// Only initialize Kafka if not mocked
if (process.env.MOCK_KAFKA !== "true") {
  try {
    kafka = new Kafka({
      clientId: process.env.KAFKA_CLIENT_ID || "geo-service",
      brokers: (process.env.KAFKA_BROKERS || "localhost:9092").split(","),
    });
    producer = kafka.producer();

    // Connect the producer
    producer
      .connect()
      .then(() => {
        console.log("Kafka producer connected");
      })
      .catch((err: Error) => {
        console.error("Failed to connect Kafka producer:", err.message);
      });
  } catch (error) {
    console.error("Error initializing Kafka:", error);
  }
}

/**
 * Publish a message to a Kafka topic if Kafka is available
 */
async function publishToKafka(topic: string, message: any): Promise<boolean> {
  if (process.env.MOCK_KAFKA === "true" || !producer) {
    console.log(`[MOCK] Publishing to Kafka topic ${topic}:`, message);
    return true;
  }

  try {
    await producer.send({
      topic,
      messages: [{ value: JSON.stringify(message) }],
    });
    return true;
  } catch (error) {
    console.error(`Error publishing to Kafka topic ${topic}:`, error);
    return false;
  }
}

export class LocationService {
  /**
   * Update driver location
   */
  async updateDriverLocation(data: LocationUpdate): Promise<boolean> {
    try {
      const {
        driverId,
        latitude,
        longitude,
        heading = 0,
        speed = 0,
        accuracy = 0,
        status = "online",
      } = data;

      // Add to Redis GEO index
      await geoUtils.addDriver(driverId, longitude, latitude);

      // Store additional data in Redis hash
      const driverDataKey = `driver:${driverId}:data`;
      await geoUtils.redis.hset(driverDataKey, {
        heading: heading.toString(),
        speed: speed.toString(),
        accuracy: accuracy.toString(),
        lastUpdated: Date.now().toString(),
      });

      // Set driver status with expiry
      await geoUtils.setDriverStatus(driverId, status, 300);

      // Update PostgreSQL/PostGIS database
      const client = await pool.connect();
      try {
        await client.query(
          "SELECT geo.update_driver_location($1, $2, $3, $4, $5, $6, $7)",
          [driverId, longitude, latitude, heading, speed, accuracy, status]
        );
      } finally {
        client.release();
      }

      // Publish location update to Kafka
      await publishToKafka("driver-locations", {
        driverId,
        latitude,
        longitude,
        heading,
        speed,
        accuracy,
        status,
        timestamp: new Date().toISOString(),
      });

      return true;
    } catch (error) {
      console.error("Error updating driver location:", error);
      return false;
    }
  }

  /**
   * Get nearby drivers
   */
  async getNearbyDrivers(
    latitude: number,
    longitude: number,
    radiusKm: number = 5,
    limit: number = 10,
    vehicleType?: string
  ): Promise<any[]> {
    try {
      // Find nearby drivers from Redis
      const redisResults = await geoUtils.findNearbyDrivers(
        longitude,
        latitude,
        radiusKm,
        limit
      );

      if (!redisResults || redisResults.length === 0) {
        return [];
      }

      // Get additional details for each driver
      const driversWithDetails = await Promise.all(
        redisResults.map(async (item: any) => {
          const [driverId, distance, coordinates] = item;
          const status = await geoUtils.getDriverStatus(driverId);
          const driverDataKey = `driver:${driverId}:data`;
          const driverData = await geoUtils.redis.hgetall(driverDataKey);

          // If vehicleType is specified, get vehicle type from core service
          // This is a placeholder - in a real implementation, you'd either store
          // this in Redis or make an API call to the core service
          const vehicleTypeInfo = await this.getDriverVehicleType(driverId);

          // Filter by vehicle type if specified
          if (vehicleType && vehicleTypeInfo !== vehicleType) {
            return null; // Skip this driver if vehicle type doesn't match
          }

          return {
            driverId,
            distance: parseFloat(distance),
            location: {
              latitude: parseFloat(coordinates[1]),
              longitude: parseFloat(coordinates[0]),
            },
            heading: driverData.heading
              ? parseFloat(driverData.heading)
              : undefined,
            speed: driverData.speed ? parseFloat(driverData.speed) : undefined,
            status: status || "unknown",
            lastUpdated: driverData.lastUpdated
              ? new Date(parseInt(driverData.lastUpdated, 10))
              : new Date(),
            vehicleType: vehicleTypeInfo,
          };
        })
      );

      // Filter out null values (drivers that didn't match vehicle type)
      // and drivers that aren't online or available
      return driversWithDetails.filter(
        (driver) =>
          driver !== null &&
          (driver.status === "online" || driver.status === "available")
      );
    } catch (error) {
      console.error("Error getting nearby drivers:", error);
      return [];
    }
  }

  /**
   * Get driver's vehicle type
   * This is a placeholder - in a real implementation, you'd either store
   * this in Redis or make an API call to the core service
   */
  async getDriverVehicleType(driverId: string): Promise<string> {
    // For now, we'll return a random vehicle type
    // In a real implementation, you'd query this from the core service
    const types = ["Économie", "Classique", "Femme"];
    const randomIndex = Math.floor(Math.random() * types.length);
    return types[randomIndex];
  }

  /**
   * Get driver location
   */
  async getDriverLocation(driverId: string): Promise<any | null> {
    try {
      const location = await geoUtils.getDriverPosition(driverId);
      if (!location) {
        return null;
      }

      const status = await geoUtils.getDriverStatus(driverId);
      const driverDataKey = `driver:${driverId}:data`;
      const driverData = await geoUtils.redis.hgetall(driverDataKey);

      return {
        driverId,
        location: {
          latitude: location[1],
          longitude: location[0],
        },
        heading: driverData.heading
          ? parseFloat(driverData.heading)
          : undefined,
        speed: driverData.speed ? parseFloat(driverData.speed) : undefined,
        status: status || "unknown",
        lastUpdated: driverData.lastUpdated
          ? new Date(parseInt(driverData.lastUpdated, 10))
          : new Date(),
      };
    } catch (error) {
      console.error("Error getting driver location:", error);
      return null;
    }
  }

  /**
   * Get driver density heatmap
   */
  async getDriverHeatmap(
    minLat: number,
    minLng: number,
    maxLat: number,
    maxLng: number,
    resolution: number = 10
  ): Promise<any[]> {
    try {
      // Get all drivers in the bounding box from Redis
      const drivers = await geoUtils.getDriversInBoundingBox(
        minLng,
        minLat,
        maxLng,
        maxLat,
        1000 // Get up to 1000 drivers
      );

      if (!drivers || drivers.length === 0) {
        return [];
      }

      // Create a grid for the heatmap
      const latStep = (maxLat - minLat) / resolution;
      const lngStep = (maxLng - minLng) / resolution;

      // Initialize grid
      const grid: {
        [key: string]: { lat: number; lng: number; weight: number };
      } = {};

      // Place drivers in grid cells
      for (const driver of drivers) {
        const [driverId, distance, coordinates] = driver;
        const lat = parseFloat(coordinates[1]);
        const lng = parseFloat(coordinates[0]);

        // Calculate grid cell indices
        const latIndex = Math.floor((lat - minLat) / latStep);
        const lngIndex = Math.floor((lng - minLng) / lngStep);

        // Create cell key
        const cellKey = `${latIndex}-${lngIndex}`;

        // Create or update cell
        if (!grid[cellKey]) {
          const cellLat = minLat + latIndex * latStep + latStep / 2;
          const cellLng = minLng + lngIndex * lngStep + lngStep / 2;
          grid[cellKey] = { lat: cellLat, lng: cellLng, weight: 0 };
        }

        // Increment weight
        grid[cellKey].weight++;
      }

      // Convert grid to array
      return Object.values(grid);
    } catch (error) {
      console.error("Error generating heatmap:", error);
      return [];
    }
  }

  /**
   * Calculate route and estimate arrival time between two points
   */
  async estimateArrival(
    fromLat: number,
    fromLng: number,
    toLat: number,
    toLng: number,
    waypoints?: Array<{ lat: number; lng: number }>
  ): Promise<{ estimatedMinutes: number; distanceKm: number }> {
    try {
      // For now, we'll use a simple direct distance calculation
      // In a real application, you would use a routing service like OSRM or Google Maps
      const distanceKm = this.calculateDistance(fromLat, fromLng, toLat, toLng);

      // Assuming average speed of 30 km/h for city travel
      const estimatedMinutes = Math.round((distanceKm / 30) * 60);

      return {
        estimatedMinutes,
        distanceKm,
      };
    } catch (error) {
      console.error("Error estimating arrival:", error);
      return {
        estimatedMinutes: 0,
        distanceKm: 0,
      };
    }
  }

  /**
   * Calculate distance between two points (Haversine formula)
   */
  calculateDistance(
    lat1: number,
    lon1: number,
    lat2: number,
    lon2: number
  ): number {
    const R = 6371; // Earth's radius in km
    const dLat = this.toRad(lat2 - lat1);
    const dLon = this.toRad(lon2 - lon1);
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(this.toRad(lat1)) *
        Math.cos(this.toRad(lat2)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c;
    return parseFloat(distance.toFixed(1)); // Round to 1 decimal place
  }

  /**
   * Convert degrees to radians
   */
  toRad(degrees: number): number {
    return (degrees * Math.PI) / 180;
  }

  /**
   * Get ride progress for a specific ride
   */
  async getRideProgress(rideId: string): Promise<RideProgress | null> {
    try {
      // This is a placeholder. In a real implementation, you would:
      // 1. Get the ride route from your database
      // 2. Get the driver's current location
      // 3. Calculate the progress along the route

      // For now, we'll return mock data
      return {
        progress: Math.random() * 100, // 0-100%
        remainingDistance: Math.random() * 10, // 0-10 km
        remainingTime: Math.floor(Math.random() * 30), // 0-30 minutes
        currentLocation: {
          lat: 36.7 + Math.random() * 0.1, // Random location near 36.7, 3.2
          lng: 3.2 + Math.random() * 0.1,
        },
      };
    } catch (error) {
      console.error("Error getting ride progress:", error);
      return null;
    }
  }
}
