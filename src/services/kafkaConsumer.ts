import { Kafka, Consumer } from "kafkajs";
import dotenv from "dotenv";
import { LocationService } from "./locationService";
import { coreService } from "./coreService";
import { geoUtils } from "../utils/redis";

dotenv.config();

// Define topics
const TOPICS = {
  DRIVER_LOCATIONS: "driver-locations",
  RIDER_LOCATIONS: "rider-locations",
  RIDE_REQUESTS: "ride-requests",
  RIDE_EVENTS: "ride-events",
  RIDE_MESSAGES: "ride-messages",
};

class KafkaConsumerService {
  private kafka: Kafka | null = null;
  private consumer: Consumer | null = null;
  private locationService: LocationService;
  private isMocked: boolean;

  constructor() {
    this.locationService = new LocationService();
    this.isMocked = process.env.MOCK_KAFKA === "true";

    if (!this.isMocked) {
      try {
        this.kafka = new Kafka({
          clientId: process.env.KAFKA_CLIENT_ID || "geo-service-consumer",
          brokers: (process.env.KAFKA_BROKERS || "localhost:9092").split(","),
        });

        this.consumer = this.kafka.consumer({
          groupId: process.env.KAFKA_GROUP_ID || "geo-service-group",
        });
      } catch (error) {
        console.error("Error initializing Kafka:", error);
      }
    } else {
      console.log("Using mock Kafka consumer");
    }
  }

  /**
   * Start the Kafka consumer
   */
  async start(): Promise<void> {
    if (this.isMocked) {
      console.log("Mock Kafka consumer started");
      return;
    }

    if (!this.consumer) {
      console.error("Kafka consumer not initialized");
      return;
    }

    try {
      // Connect the consumer
      await this.consumer.connect();
      console.log("Kafka consumer connected");

      // Subscribe to topics
      await this.consumer.subscribe({
        topics: [TOPICS.RIDE_REQUESTS, TOPICS.RIDE_EVENTS],
        fromBeginning: false,
      });

      // Start consuming messages
      await this.consumer.run({
        eachMessage: async ({ topic, partition, message }) => {
          try {
            if (!message.value) return;

            const messageData = JSON.parse(message.value.toString());
            console.log(
              `Received Kafka message from topic ${topic}:`,
              messageData.type
            );

            await this.processMessage(topic, messageData);
          } catch (error) {
            console.error("Error processing Kafka message:", error);
          }
        },
      });

      console.log("Kafka consumer started");
    } catch (error) {
      console.error("Error starting Kafka consumer:", error);
    }
  }

  /**
   * Stop the Kafka consumer
   */
  async stop(): Promise<void> {
    if (this.isMocked || !this.consumer) return;

    try {
      await this.consumer.disconnect();
      console.log("Kafka consumer disconnected");
    } catch (error) {
      console.error("Error stopping Kafka consumer:", error);
    }
  }

  /**
   * Process a message from Kafka
   */
  private async processMessage(topic: string, message: any): Promise<void> {
    switch (topic) {
      case TOPICS.RIDE_REQUESTS:
        await this.processRideRequestMessage(message);
        break;
      case TOPICS.RIDE_EVENTS:
        await this.processRideEventMessage(message);
        break;
      default:
        console.warn(`Unhandled Kafka topic: ${topic}`);
    }
  }

  /**
   * Process ride request messages
   */
  private async processRideRequestMessage(message: any): Promise<void> {
    switch (message.type) {
      case "NEW_RIDE_REQUEST":
        await this.handleNewRideRequest(message);
        break;
      default:
        console.warn(`Unhandled ride request message type: ${message.type}`);
    }
  }

  /**
   * Process ride event messages
   */
  private async processRideEventMessage(message: any): Promise<void> {
    switch (message.type) {
      case "RIDE_ACCEPTED":
        await this.handleRideAccepted(message);
        break;
      case "RIDE_STARTED":
        await this.handleRideStarted(message);
        break;
      case "RIDE_COMPLETED":
        await this.handleRideCompleted(message);
        break;
      case "RIDE_CANCELLED":
        await this.handleRideCancelled(message);
        break;
      default:
        console.warn(`Unhandled ride event message type: ${message.type}`);
    }
  }

  /**
   * Handle new ride request
   * This is where we'd find the nearest drivers and notify them
   */
  private async handleNewRideRequest(message: any): Promise<void> {
    try {
      console.log(`Processing new ride request: ${message.rideId}`);

      // Find nearby drivers
      const nearbyDrivers = await this.locationService.getNearbyDrivers(
        message.pickupLocation.lat,
        message.pickupLocation.lng,
        5, // 5km radius
        10, // Limit to 10 drivers
        message.requestedVehicleType
      );

      if (nearbyDrivers.length === 0) {
        console.log(`No nearby drivers found for ride ${message.rideId}`);
        return;
      }

      // In a real implementation, you would:
      // 1. Store this ride request in a database
      // 2. Notify drivers in order of proximity
      // 3. Set a timeout for driver acceptance

      console.log(
        `Found ${nearbyDrivers.length} drivers for ride ${message.rideId}`
      );

      // For now, we'll just log the drivers found
      nearbyDrivers.forEach((driver: any, index: number) => {
        console.log(
          `Driver ${index + 1}: ${driver.driverId} - Distance: ${
            driver.distance
          }km`
        );
      });
    } catch (error) {
      console.error(`Error handling new ride request: ${error}`);
    }
  }

  /**
   * Handle ride accepted event
   */
  private async handleRideAccepted(message: any): Promise<void> {
    try {
      console.log(
        `Processing ride accepted: ${message.rideId} by driver ${message.driverId}`
      );

      // Get driver's current location
      const driverLocation = await this.locationService.getDriverLocation(
        message.driverId
      );
      if (!driverLocation) {
        console.log(`Driver ${message.driverId} location not found`);
        return;
      }

      // Get ride details to find the pickup location
      const rideDetails = await coreService.getRideDetails(message.rideId);
      if (!rideDetails) {
        console.log(`Ride ${message.rideId} details not found`);
        return;
      }

      // Calculate ETA
      const eta = await this.locationService.estimateArrival(
        driverLocation.location.latitude,
        driverLocation.location.longitude,
        rideDetails.pickupLocation.lat,
        rideDetails.pickupLocation.lng
      );

      // Update driver status in Redis
      await geoUtils.setDriverStatus(message.driverId, "busy");

      // Notify the core service about the estimated arrival
      await coreService.notifyDriverMatched(
        message.rideId,
        message.driverId,
        eta.estimatedMinutes
      );

      console.log(
        `Driver ${message.driverId} estimated arrival: ${eta.estimatedMinutes} minutes`
      );
    } catch (error) {
      console.error(`Error handling ride accepted: ${error}`);
    }
  }

  /**
   * Handle ride started event
   */
  private async handleRideStarted(message: any): Promise<void> {
    try {
      console.log(
        `Processing ride started: ${message.rideId} by driver ${message.driverId}`
      );

      // Update driver status in Redis
      await geoUtils.setDriverStatus(message.driverId, "busy");

      // In a real implementation, you would:
      // 1. Start tracking the ride progress
      // 2. Set up route monitoring
      // 3. Calculate estimated arrival at destination
    } catch (error) {
      console.error(`Error handling ride started: ${error}`);
    }
  }

  /**
   * Handle ride completed event
   */
  private async handleRideCompleted(message: any): Promise<void> {
    try {
      console.log(
        `Processing ride completed: ${message.rideId} by driver ${message.driverId}`
      );

      // Update driver status in Redis
      await geoUtils.setDriverStatus(message.driverId, "online");

      // In a real implementation, you would:
      // 1. Update ride statistics
      // 2. Store the completed route
      // 3. Clean up any tracking resources
    } catch (error) {
      console.error(`Error handling ride completed: ${error}`);
    }
  }

  /**
   * Handle ride cancelled event
   */
  private async handleRideCancelled(message: any): Promise<void> {
    try {
      console.log(
        `Processing ride cancelled: ${message.rideId}, cancelled by: ${message.cancelledBy}`
      );

      // If cancelled by rider and we have a driver assigned, update driver status
      if (message.cancelledBy === "rider" && message.driverId) {
        await geoUtils.setDriverStatus(message.driverId, "online");
      }

      // In a real implementation, you would:
      // 1. Clean up any pending ride requests
      // 2. Notify other services if needed
    } catch (error) {
      console.error(`Error handling ride cancelled: ${error}`);
    }
  }
}
