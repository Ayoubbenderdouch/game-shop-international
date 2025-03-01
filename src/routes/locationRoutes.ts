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
}

interface GetDriverLocationParams {
  driverId: string;
}

const locationService = new LocationService();

export default async function locationRoutes(fastify: FastifyInstance) {
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
            driverId: { type: "string", format: "uuid" },
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
              },
            },
          },
        },
      },
    },
    async (request, reply) => {
      const { latitude, longitude, radiusKm, limit } = request.query;

      const drivers = await locationService.getNearbyDrivers(
        latitude,
        longitude,
        radiusKm,
        limit
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
            driverId: { type: "string", format: "uuid" },
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
              status: { type: "string" },
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
}
