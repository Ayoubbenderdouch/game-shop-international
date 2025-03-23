import { FastifyInstance, FastifyRequest, FastifyReply } from "fastify";
import { LocationService } from "../services/locationService";

interface UpdateLocationParams {
  driverId: string;
}

interface UpdateLocationBody {
  latitude: number;
  longitude: number;
  heading?: number;
  speed?: number;
  accuracy?: number;
  status?: string;
}

interface NearbyDriversQuery {
  latitude: number;
  longitude: number;
  radiusKm?: number;
  limit?: number;
  vehicleType?: string;
}

interface GetDriverLocationParams {
  driverId: string;
}

interface GetHeatmapQuery {
  boundaries: {
    sw: { lat: number; lng: number };
    ne: { lat: number; lng: number };
  };
  resolution?: number;
}

interface RouteEstimationBody {
  origin: {
    lat: number;
    lng: number;
  };
  destination: {
    lat: number;
    lng: number;
  };
  waypoints?: Array<{
    lat: number;
    lng: number;
  }>;
}

const locationService = new LocationService();

export default async function locationRoutes(fastify: FastifyInstance) {
  // Update driver location
  fastify.put<{
    Params: UpdateLocationParams;
    Body: UpdateLocationBody;
  }>(
    "/location/drivers/:driverId",
    {
      schema: {
        params: {
          type: "object",
          required: ["driverId"],
          properties: {
            driverId: { type: "string" },
          },
        },
        body: {
          type: "object",
          required: ["latitude", "longitude"],
          properties: {
            latitude: { type: "number", minimum: -90, maximum: 90 },
            longitude: { type: "number", minimum: -180, maximum: 180 },
            heading: { type: "number", minimum: 0, maximum: 360 },
            speed: { type: "number", minimum: 0 },
            accuracy: { type: "number", minimum: 0 },
            status: {
              type: "string",
              enum: ["online", "offline", "busy", "available"],
            },
          },
        },
        response: {
          200: {
            type: "object",
            properties: {
              success: { type: "boolean" },
            },
          },
        },
      },
    },
    async (request, reply) => {
      const { driverId } = request.params;
      const { latitude, longitude, heading, speed, accuracy, status } =
        request.body;

      const success = await locationService.updateDriverLocation({
        driverId,
        latitude,
        longitude,
        heading,
        speed,
        accuracy,
        status,
      });

      return { success };
    }
  );

  // Get nearby drivers
  fastify.get<{
    Querystring: NearbyDriversQuery;
  }>(
    "/location/drivers/nearby",
    {
      schema: {
        querystring: {
          type: "object",
          required: ["latitude", "longitude"],
          properties: {
            latitude: { type: "number", minimum: -90, maximum: 90 },
            longitude: { type: "number", minimum: -180, maximum: 180 },
            radiusKm: { type: "number", minimum: 0.1, maximum: 50, default: 5 },
            limit: { type: "number", minimum: 1, maximum: 100, default: 10 },
            vehicleType: { type: "string" },
          },
        },
        response: {
          200: {
            type: "array",
            items: {
              type: "object",
              properties: {
                driverId: { type: "string" },
                distance: { type: "number" },
                location: {
                  type: "object",
                  properties: {
                    latitude: { type: "number" },
                    longitude: { type: "number" },
                  },
                },
                heading: { type: "number" },
                speed: { type: "number" },
                status: { type: "string" },
                lastUpdated: { type: "string", format: "date-time" },
                vehicleType: { type: "string" },
              },
            },
          },
        },
      },
    },
    async (request, reply) => {
      const { latitude, longitude, radiusKm, limit, vehicleType } =
        request.query;

      const drivers = await locationService.getNearbyDrivers(
        latitude,
        longitude,
        radiusKm,
        limit,
        vehicleType
      );

      return drivers;
    }
  );

  // Get driver location
  fastify.get<{
    Params: GetDriverLocationParams;
  }>(
    "/location/drivers/:driverId",
    {
      schema: {
        params: {
          type: "object",
          required: ["driverId"],
          properties: {
            driverId: { type: "string" },
          },
        },
        response: {
          200: {
            type: "object",
            properties: {
              driverId: { type: "string" },
              location: {
                type: "object",
                properties: {
                  latitude: { type: "number" },
                  longitude: { type: "number" },
                },
              },
              heading: { type: "number" },
              speed: { type: "number" },
              status: { type: "string" },
              lastUpdated: { type: "string", format: "date-time" },
            },
          },
          404: {
            type: "object",
            properties: {
              error: { type: "string" },
            },
          },
        },
      },
    },
    async (request, reply) => {
      const { driverId } = request.params;

      const driverLocation = await locationService.getDriverLocation(driverId);

      if (!driverLocation) {
        reply.code(404);
        return { error: "Driver location not found" };
      }

      return driverLocation;
    }
  );

  // Get driver density heatmap
  fastify.get(
    "/location/heatmap",
    {
      schema: {
        querystring: {
          type: "object",
          required: ["boundaries"],
          properties: {
            boundaries: {
              type: "object",
              properties: {
                sw: {
                  type: "object",
                  properties: {
                    lat: { type: "number" },
                    lng: { type: "number" },
                  },
                },
                ne: {
                  type: "object",
                  properties: {
                    lat: { type: "number" },
                    lng: { type: "number" },
                  },
                },
              },
            },
            resolution: { type: "number", default: 10 },
          },
        },
        response: {
          200: {
            type: "object",
            properties: {
              points: {
                type: "array",
                items: {
                  type: "object",
                  properties: {
                    lat: { type: "number" },
                    lng: { type: "number" },
                    weight: { type: "number" },
                  },
                },
              },
            },
          },
        },
      },
    },
    async (request, reply) => {
      const { boundaries, resolution } =
        request.query as unknown as GetHeatmapQuery;

      // Calculate heatmap data
      const heatmapData = await locationService.getDriverHeatmap(
        boundaries.sw.lat,
        boundaries.sw.lng,
        boundaries.ne.lat,
        boundaries.ne.lng,
        resolution || 10
      );

      return { points: heatmapData };
    }
  );

  // Estimate arrival time between two points
  fastify.post(
    "/routes/estimate-arrival",
    {
      schema: {
        body: {
          type: "object",
          required: ["origin", "destination"],
          properties: {
            origin: {
              type: "object",
              properties: {
                lat: { type: "number" },
                lng: { type: "number" },
              },
            },
            destination: {
              type: "object",
              properties: {
                lat: { type: "number" },
                lng: { type: "number" },
              },
            },
            waypoints: {
              type: "array",
              items: {
                type: "object",
                properties: {
                  lat: { type: "number" },
                  lng: { type: "number" },
                },
              },
            },
          },
        },
        response: {
          200: {
            type: "object",
            properties: {
              estimatedMinutes: { type: "number" },
              distanceKm: { type: "number" },
            },
          },
        },
      },
    },
    async (request, reply) => {
      const { origin, destination, waypoints } =
        request.body as RouteEstimationBody;

      // Calculate estimated arrival time
      const result = await locationService.estimateArrival(
        origin.lat,
        origin.lng,
        destination.lat,
        destination.lng,
        waypoints
      );

      return result;
    }
  );

  // Track ride progress
  fastify.get(
    "/rides/:rideId/progress",
    {
      schema: {
        params: {
          type: "object",
          required: ["rideId"],
          properties: {
            rideId: { type: "string" },
          },
        },
        response: {
          200: {
            type: "object",
            properties: {
              progress: { type: "number" },
              remainingDistance: { type: "number" },
              remainingTime: { type: "number" },
              currentLocation: {
                type: "object",
                properties: {
                  lat: { type: "number" },
                  lng: { type: "number" },
                },
              },
            },
          },
          404: {
            type: "object",
            properties: {
              error: { type: "string" },
            },
          },
        },
      },
    },
    async (request, reply) => {
      const { rideId } = request.params as { rideId: string };

      const progress = await locationService.getRideProgress(rideId);

      if (!progress) {
        reply.code(404);
        return { error: "Ride not found or not in progress" };
      }

      return progress;
    }
  );
}
