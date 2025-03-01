import Redis from "ioredis";
import dotenv from "dotenv";

dotenv.config();

const redis = new Redis({
  host: process.env.REDIS_HOST || "redis",
  port: parseInt(process.env.REDIS_PORT || "6379", 10),
  password: process.env.REDIS_PASSWORD,
  lazyConnect: true,
  enableReadyCheck: true,
  maxRetriesPerRequest: 3,
  retryStrategy(times) {
    const delay = Math.min(times * 50, 2000);
    return delay;
  },
});

// Handle Redis connection events
redis.on("connect", () => {
  console.log("Connected to Redis server");
});

redis.on("error", (err) => {
  console.error("Redis connection error:", err);
});

export const geoUtils = {
  redis,

  addDriver: async (
    driverId: string,
    longitude: number,
    latitude: number
  ): Promise<number> => {
    return redis.geoadd("drivers:locations", longitude, latitude, driverId);
  },

  getDriverPosition: async (
    driverId: string
  ): Promise<[number, number] | null> => {
    const position = await redis.geopos("drivers:locations", driverId);
    if (position && position[0]) {
      return [parseFloat(position[0][0]), parseFloat(position[0][1])];
    }
    return null;
  },

  findNearbyDrivers: async (
    longitude: number,
    latitude: number,
    radiusKm: number,
    limit: number = 10
  ) => {
    return redis.georadius(
      "drivers:locations",
      longitude,
      latitude,
      radiusKm,
      "km",
      "WITHDIST",
      "WITHCOORD",
      "COUNT",
      limit,
      "ASC"
    );
  },

  // Calculate distance between driver and a point
  calculateDistance: async (
    driverId: string,
    longitude: number,
    latitude: number
  ): Promise<number | null> => {
    const distance = (await redis.call(
      "GEODIST",
      "drivers:locations",
      driverId,
      [longitude, latitude].join(","),
      "km"
    )) as string | null;

    return distance ? parseFloat(distance) : null;
  },

  setDriverStatus: async (
    driverId: string,
    status: string,
    expirySeconds: number = 300
  ): Promise<void> => {
    await redis.set(`driver:${driverId}:status`, status, "EX", expirySeconds);
  },

  getDriverStatus: async (driverId: string): Promise<string | null> => {
    return redis.get(`driver:${driverId}:status`);
  },

  removeOfflineDriver: async (driverId: string): Promise<number> => {
    return redis.zrem("drivers:locations", driverId);
  },

  getDriversInBoundingBox: async (
    minLng: number,
    minLat: number,
    maxLng: number,
    maxLat: number,
    limit: number = 100
  ) => {
    return redis.call(
      "GEOSEARCH",
      "drivers:locations",
      "FROMLONLAT",
      (minLng + maxLng) / 2,
      (minLat + maxLat) / 2,
      "BYBOX",
      maxLng - minLng,
      maxLat - minLat,
      "km",
      "WITHDIST",
      "WITHCOORD",
      "COUNT",
      limit
    );
  },
};

export default redis;
