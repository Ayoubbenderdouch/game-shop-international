import axios from "axios";
import dotenv from "dotenv";

dotenv.config();

/**
 * Service to communicate with the Core Service (VPS 1)
 */
export class CoreService {
  private baseUrl: string;
  private axios: any;

  constructor() {
    this.baseUrl = process.env.CORE_SERVICE_URL || "http://core-server:3000";
    this.axios = axios.create({
      baseURL: this.baseUrl,
      timeout: 10000,
    });

    // Add request interceptor for logging
    this.axios.interceptors.request.use(
      (config: any) => {
        console.debug(
          `CoreService Request: ${config.method.toUpperCase()} ${config.url}`
        );
        return config;
      },
      (error: any) => {
        console.error(`CoreService Request Error: ${error.message}`);
        return Promise.reject(error);
      }
    );

    // Add response interceptor for logging
    this.axios.interceptors.response.use(
      (response: any) => {
        console.debug(
          `CoreService Response: ${response.status} for ${response.config.url}`
        );
        return response;
      },
      (error: any) => {
        if (error.response) {
          console.error(
            `CoreService Error: ${error.response.status} - ${JSON.stringify(
              error.response.data
            )}`
          );
        } else if (error.request) {
          console.error(
            `CoreService Error: No response received - ${error.message}`
          );
        } else {
          console.error(`CoreService Error: ${error.message}`);
        }
        return Promise.reject(error);
      }
    );
  }

  /**
   * Get driver details including vehicle information
   */
  async getDriverDetails(driverId: string): Promise<any> {
    try {
      const response = await this.axios.get(`/api/v1/drivers/${driverId}`);
      return response.data.data.driver;
    } catch (error) {
      console.error(`Error getting driver details: ${error}`);
      return null;
    }
  }

  /**
   * Get ride details
   */
  async getRideDetails(rideId: string): Promise<any> {
    try {
      const response = await this.axios.get(`/api/v1/rides/${rideId}`);
      return response.data.data.ride;
    } catch (error) {
      console.error(`Error getting ride details: ${error}`);
      return null;
    }
  }

  /**
   * Update ride progress
   */
  async updateRideProgress(
    rideId: string,
    progress: number,
    remainingDistance: number,
    remainingTime: number
  ): Promise<boolean> {
    try {
      await this.axios.post(`/api/v1/rides/${rideId}/progress`, {
        progress,
        remainingDistance,
        remainingTime,
        timestamp: new Date().toISOString(),
      });
      return true;
    } catch (error) {
      console.error(`Error updating ride progress: ${error}`);
      return false;
    }
  }

  /**
   * Notify driver-rider matching
   */
  async notifyDriverMatched(
    rideId: string,
    driverId: string,
    estimatedArrival: number
  ): Promise<boolean> {
    try {
      await this.axios.post(`/api/v1/rides/${rideId}/match`, {
        driverId,
        estimatedArrival,
        timestamp: new Date().toISOString(),
      });
      return true;
    } catch (error) {
      console.error(`Error notifying driver matched: ${error}`);
      return false;
    }
  }

  /**
   * Check health status of core service
   */
  async checkHealth(): Promise<boolean> {
    try {
      const response = await this.axios.get("/health");
      return (
        response.data.status === "ok" || response.data.status === "success"
      );
    } catch (error) {
      console.error(`Core service health check failed: ${error}`);
      return false;
    }
  }
}

// Export a singleton instance
export const coreService = new CoreService();
