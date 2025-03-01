import pool from "../utils/database";
import { geoUtils } from "../utils/redis";

interface LocationUpdate {
  driverId: string;
  latitude: number;
  longitude: number;
  heading?: number;
  speed?: number;
  accuracy?: number;
  status?: string;
}

export class LocationService {
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

      await geoUtils.addDriver(driverId, longitude, latitude);

      const driverDataKey = `driver:${driverId}:data`;
      await geoUtils.redis.hset(driverDataKey, {
        heading: heading.toString(),
        speed: speed.toString(),
        accuracy: accuracy.toString(),
        lastUpdated: Date.now().toString(),
      });

      await geoUtils.setDriverStatus(driverId, status, 300);

      pool
        .connect()
        .then((client) => {
          client
            .query(
              "SELECT geo.update_driver_location($1, $2, $3, $4, $5, $6, $7)",
              [driverId, longitude, latitude, heading, speed, accuracy, status]
            )
            .finally(() => client.release());
        })
        .catch((err) => {
          console.error("Error updating PostGIS:", err);
        });

      return true;
    } catch (error) {
      console.error("Error updating driver location:", error);
      return false;
    }
  }

  async getNearbyDrivers(
    latitude: number,
    longitude: number,
    radiusKm: number = 5,
    limit: number = 10
  ): Promise<any[]> {
    try {
      const redisResults = await geoUtils.findNearbyDrivers(
        longitude,
        latitude,
        radiusKm,
        limit
      );

      if (!redisResults || redisResults.length === 0) {
        return [];
      }

      const driversWithDetails = await Promise.all(
        redisResults.map(async (item: any) => {
          const [driverId, distance, coordinates] = item;
          const status = await geoUtils.getDriverStatus(driverId);
          const driverDataKey = `driver:${driverId}:data`;
          const driverData = await geoUtils.redis.hgetall(driverDataKey);
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
          };
        })
      );

      return driversWithDetails.filter(
        (driver) => driver.status === "online" || driver.status === "available"
      );
    } catch (error) {
      console.error("Error getting nearby drivers:", error);
      return [];
    }
  }

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
}
