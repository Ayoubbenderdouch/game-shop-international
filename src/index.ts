import Fastify, { FastifyInstance } from "fastify";
import fastifyRedis from "@fastify/redis";
import fastifyHelmet from "@fastify/helmet";
import fastifyCors from "@fastify/cors";
import dotenv from "dotenv";
import { checkDatabaseConnection } from "./utils/database";
import redis from "./utils/redis";
import locationRoutes from "./routes/locationRoutes";


dotenv.config();

const server: FastifyInstance = Fastify({
  logger: {
    level: process.env.NODE_ENV === "development" ? "debug" : "info",
  },
  trustProxy: true, 
});

// Register plugins
server.register(fastifyHelmet);
server.register(fastifyCors, {
  origin: process.env.NODE_ENV === "production" ? false : "*",
  methods: ["GET", "PUT", "POST", "DELETE"],
});

server.register(fastifyRedis, {
  client: redis,
});

server.get("/health", async () => {
  const dbConnected = await checkDatabaseConnection();
  let redisConnected = false;
  try {
    await server.redis.ping();
    redisConnected = true;
  } catch (error) {
    server.log.error("Redis health check failed:", error);
  }

  const healthy = dbConnected && redisConnected;

  return {
    status: healthy ? "ok" : "degraded",
    timestamp: new Date().toISOString(),
    services: {
      database: dbConnected ? "connected" : "disconnected",
      redis: redisConnected ? "connected" : "disconnected",
    },
  };
});

server.get("/", async (request, reply) => {
  return {
    message: "Geo Location Server - Running",
    service: "Location Services - Fitrigi",
    version: "1.0.0",
    endpoints: [
      { path: "/health", description: "Health check endpoint" },
      {
        path: "/location/drivers/:driverId",
        description: "Update or get driver location",
      },
      { path: "/location/drivers/nearby", description: "Find nearby drivers" },
    ],
  };
});

server.register(locationRoutes);

const start = async () => {
  try {
    const port = process.env.PORT ? parseInt(process.env.PORT, 10) : 3001;
    await server.listen({ port, host: "0.0.0.0" });

    const address = server.server.address();
    const port_used = typeof address === "string" ? address : address?.port;

    console.log(`🚀 Geo Server started successfully on port ${port_used}`);
  } catch (err) {
    server.log.error(err);
    process.exit(1);
  }
};

process.on("SIGINT", () => server.close());
process.on("SIGTERM", () => server.close());

start();
