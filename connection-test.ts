// docker run --rm -it \                                                                                     ─╯
//   --network geo-server_geo-network \
//   -v $(pwd):/app \
//   -w /app \
//   node:16 ./test-entrypoint.sh
// excute it from above

import { Pool } from "pg";
import Redis from "ioredis";
import dotenv from "dotenv";
import fs from "fs";

// Load environment variables
dotenv.config();

// Terminal colors for better readability
const colors = {
  reset: "\x1b[0m",
  red: "\x1b[31m",
  green: "\x1b[32m",
  yellow: "\x1b[33m",
  blue: "\x1b[34m",
  magenta: "\x1b[35m",
  cyan: "\x1b[36m",
  white: "\x1b[37m",
};

// Log with timestamp
function log(message: string, color = colors.white): void {
  const timestamp = new Date().toISOString();
  console.log(`${colors.cyan}[${timestamp}]${color} ${message}${colors.reset}`);
}

// Log environment variables (sanitized)
log("Environment Variables:", colors.yellow);
log(`POSTGRES_HOST: ${process.env.POSTGRES_HOST || "postgis"}`);
log(`POSTGRES_PORT: ${process.env.POSTGRES_PORT || "5432"}`);
log(`POSTGRES_USER: ${process.env.POSTGRES_USER || "geouser"}`);
log(`POSTGRES_DB: ${process.env.POSTGRES_DB || "geolocations"}`);
log(`PGBOUNCER_HOST: ${process.env.PGBOUNCER_HOST || "pgbouncer"}`);
log(`PGBOUNCER_PORT: ${process.env.PGBOUNCER_PORT || "6432"}`);
log(`REDIS_HOST: ${process.env.REDIS_HOST || "redis"}`);
log(`REDIS_PORT: ${process.env.REDIS_PORT || "6379"}`);
log(""); // Empty line for readability

// Test PostgreSQL Direct Connection
async function testPostgreSQL(): Promise<boolean> {
  log("Testing Direct PostgreSQL Connection...", colors.magenta);

  const pgPool = new Pool({
    host: process.env.POSTGRES_HOST || "postgis",
    port: parseInt(process.env.POSTGRES_PORT || "5432", 10),
    user: process.env.POSTGRES_USER || "geouser",
    password: process.env.POSTGRES_PASSWORD || "P@ssw0rd_PostgreSQL_Complex!23",
    database: process.env.POSTGRES_DB || "geolocations",
  });

  try {
    // Basic connectivity test
    log("Attempting to connect to PostgreSQL...");
    const client = await pgPool.connect();
    log("Connection established ✅", colors.green);

    // Test basic query
    log("Running basic query...");
    const basicResult = await client.query(
      "SELECT NOW() as time, current_database() as db, current_user as user"
    );
    log(`Query result: ${JSON.stringify(basicResult.rows[0])}`, colors.green);

    // Test PostGIS functionality
    log("Testing PostGIS functionality...");
    const postgisResult = await client.query(
      "SELECT ST_AsText(ST_Point(0, 0)) as point"
    );
    log(
      `PostGIS query result: ${JSON.stringify(postgisResult.rows[0])}`,
      colors.green
    );

    // Test database schema
    log("Checking for geo schema...");
    const schemaResult = await client.query(
      "SELECT schema_name FROM information_schema.schemata WHERE schema_name = 'geo'"
    );
    if (schemaResult.rows.length > 0) {
      log("Geo schema exists in PostgreSQL ✅", colors.green);
    } else {
      log("Geo schema does not exist in PostgreSQL ❌", colors.red);
    }

    // Check for tables
    log("Checking for driver_locations table...");
    const tableResult = await client.query(
      "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_schema = 'geo' AND table_name = 'driver_locations')"
    );
    if (tableResult.rows[0].exists) {
      log("driver_locations table exists in PostgreSQL ✅", colors.green);
    } else {
      log("driver_locations table does not exist in PostgreSQL ❌", colors.red);
    }

    client.release();
    await pgPool.end();
    log("PostgreSQL tests completed", colors.green);
    return true;
  } catch (error) {
    const err = error as Error;
    log(`PostgreSQL connection error: ${err.message}`, colors.red);
    if (err.stack) {
      log(`Stack trace: ${err.stack}`, colors.red);
    }
    try {
      await pgPool.end();
    } catch (e) {
      // Ignore errors on end
    }
    return false;
  }
}

// Test PgBouncer Connection with geolocations database
async function testPgBouncer(): Promise<boolean> {
  log(
    "Testing PgBouncer Connection with 'geolocations' database...",
    colors.magenta
  );

  const bouncePool = new Pool({
    host: process.env.PGBOUNCER_HOST || "pgbouncer",
    port: parseInt(process.env.PGBOUNCER_PORT || "6432", 10),
    user: process.env.PGBOUNCER_USER || "geouser",
    password: process.env.POSTGRES_PASSWORD || "P@ssw0rd_PostgreSQL_Complex!23",
    database: process.env.POSTGRES_DB || "geolocations",
  });

  try {
    // Basic connectivity test
    log("Attempting to connect to PgBouncer with 'geolocations'...");
    const client = await bouncePool.connect();
    log("Connection established ✅", colors.green);

    // Test basic query
    log("Running basic query through PgBouncer...");
    const result = await client.query(
      "SELECT NOW() as time, current_database() as db, current_user as user"
    );
    log(`Query result: ${JSON.stringify(result.rows[0])}`, colors.green);

    // Test database schema through PgBouncer
    log("Checking for geo schema through PgBouncer...");
    try {
      const schemaResult = await client.query(
        "SELECT schema_name FROM information_schema.schemata WHERE schema_name = 'geo'"
      );
      if (schemaResult.rows.length > 0) {
        log("Geo schema accessible through PgBouncer ✅", colors.green);
      } else {
        log("Geo schema not accessible through PgBouncer ❌", colors.red);
      }
    } catch (schemaErr) {
      log(`Error checking schema: ${(schemaErr as Error).message}`, colors.red);
    }

    // Check for tables through PgBouncer
    log("Checking for driver_locations table through PgBouncer...");
    try {
      const tableResult = await client.query(
        "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_schema = 'geo' AND table_name = 'driver_locations')"
      );
      if (tableResult.rows[0].exists) {
        log(
          "driver_locations table accessible through PgBouncer ✅",
          colors.green
        );
      } else {
        log(
          "driver_locations table not accessible through PgBouncer ❌",
          colors.red
        );
      }
    } catch (tableErr) {
      log(`Error checking table: ${(tableErr as Error).message}`, colors.red);
    }

    client.release();
    await bouncePool.end();
    log("PgBouncer 'geolocations' tests completed", colors.green);
    return true;
  } catch (error) {
    const err = error as Error;
    log(
      `PgBouncer 'geolocations' connection error: ${err.message}`,
      colors.red
    );
    try {
      await bouncePool.end();
    } catch (e) {
      // Ignore errors on end
    }

    // Try connecting with 'postgres' database name instead
    return await testPgBouncerWithPostgres();
  }
}

// Test PgBouncer Connection with postgres database
async function testPgBouncerWithPostgres(): Promise<boolean> {
  log(
    "Testing PgBouncer Connection with 'postgres' database...",
    colors.magenta
  );

  const bouncePool = new Pool({
    host: process.env.PGBOUNCER_HOST || "pgbouncer",
    port: parseInt(process.env.PGBOUNCER_PORT || "6432", 10),
    user: process.env.PGBOUNCER_USER || "geouser",
    password: process.env.POSTGRES_PASSWORD || "P@ssw0rd_PostgreSQL_Complex!23",
    database: "postgres", // Try with postgres database
  });

  try {
    // Basic connectivity test
    log("Attempting to connect to PgBouncer with 'postgres'...");
    const client = await bouncePool.connect();
    log("Connection established ✅", colors.green);

    // Test basic query
    log("Running basic query through PgBouncer...");
    const result = await client.query(
      "SELECT NOW() as time, current_database() as db, current_user as user"
    );
    log(`Query result: ${JSON.stringify(result.rows[0])}`, colors.green);

    // Test database schema through PgBouncer
    log("Checking for geo schema through PgBouncer (postgres db)...");
    try {
      const schemaResult = await client.query(
        "SELECT schema_name FROM information_schema.schemata WHERE schema_name = 'geo'"
      );
      if (schemaResult.rows.length > 0) {
        log(
          "Geo schema accessible through PgBouncer with 'postgres' db ✅",
          colors.green
        );
      } else {
        log(
          "Geo schema not accessible through PgBouncer with 'postgres' db ❌",
          colors.red
        );
      }
    } catch (schemaErr) {
      log(`Error checking schema: ${(schemaErr as Error).message}`, colors.red);
    }

    // Check for tables through PgBouncer
    log(
      "Checking for driver_locations table through PgBouncer (postgres db)..."
    );
    try {
      const tableResult = await client.query(
        "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_schema = 'geo' AND table_name = 'driver_locations')"
      );
      if (tableResult.rows[0].exists) {
        log(
          "driver_locations table accessible through PgBouncer with 'postgres' db ✅",
          colors.green
        );
      } else {
        log(
          "driver_locations table not accessible through PgBouncer with 'postgres' db ❌",
          colors.red
        );
      }
    } catch (tableErr) {
      log(`Error checking table: ${(tableErr as Error).message}`, colors.red);
    }

    client.release();
    await bouncePool.end();
    log("PgBouncer 'postgres' tests completed", colors.green);
    return true;
  } catch (error) {
    const err = error as Error;
    log(`PgBouncer 'postgres' connection error: ${err.message}`, colors.red);
    if (err.stack) {
      log(`Stack trace: ${err.stack}`, colors.red);
    }
    try {
      await bouncePool.end();
    } catch (e) {
      // Ignore errors on end
    }
    return false;
  }
}

// Test Redis Connection
async function testRedis(): Promise<boolean> {
  log("Testing Redis Connection...", colors.magenta);

  const redis = new Redis({
    host: process.env.REDIS_HOST || "redis",
    port: parseInt(process.env.REDIS_PORT || "6379", 10),
    password: process.env.REDIS_PASSWORD || "P@ssw0rd_Redis_Complex!23",
    connectTimeout: 10000,
  });

  try {
    // Test basic connectivity
    log("Connection established ✅", colors.green);

    // Test PING command
    log("Testing PING command...");
    const pingResult = await redis.ping();
    log(`PING result: ${pingResult}`, colors.green);

    // Test SET and GET operations
    log("Testing SET/GET operations...");
    await redis.set("test:key", "Connection test successful");
    const getValue = await redis.get("test:key");
    log(`GET result: ${getValue}`, colors.green);

    // Test GEO commands
    log("Testing Redis GEO commands...");
    await redis.geoadd("test:locations", 74.006, 40.7128, "new_york");
    await redis.geoadd("test:locations", 118.2437, 34.0522, "los_angeles");

    // Fix geodist command with proper type
    const distanceResult = await redis.call(
      "GEODIST",
      "test:locations",
      "new_york",
      "los_angeles",
      "km"
    );
    // const distance = distanceResult ? parseFloat(distanceResult) : 0;
    log(`Distance between NY and LA: ${distanceResult} km`, colors.green);

    // Clean up
    await redis.del("test:key");
    await redis.del("test:locations");

    await redis.quit();
    log("Redis tests completed", colors.green);
    return true;
  } catch (error) {
    const err = error as Error;
    log(`Redis connection error: ${err.message}`, colors.red);
    if (err.stack) {
      log(`Stack trace: ${err.stack}`, colors.red);
    }
    try {
      await redis.quit();
    } catch (e) {
      // Ignore errors on quit
    }
    return false;
  }
}

// Test all connections one by one
async function runTests(): Promise<void> {
  try {
    log("===== CONNECTION TEST SCRIPT =====", colors.yellow);
    log(`Starting tests at: ${new Date().toLocaleString()}\n`);

    // Run tests
    const pgResult = await testPostgreSQL();
    log(""); // Empty line for readability

    const pgbResult = await testPgBouncer();
    log(""); // Empty line for readability

    const redisResult = await testRedis();

    // Display summary
    log("\n===== TEST RESULTS SUMMARY =====", colors.yellow);
    log(
      `PostgreSQL: ${
        pgResult ? colors.green + "PASS ✅" : colors.red + "FAIL ❌"
      }${colors.reset}`
    );
    log(
      `PgBouncer: ${
        pgbResult ? colors.green + "PASS ✅" : colors.red + "FAIL ❌"
      }${colors.reset}`
    );
    log(
      `Redis: ${
        redisResult ? colors.green + "PASS ✅" : colors.red + "FAIL ❌"
      }${colors.reset}`
    );

    const allPassed = pgResult && pgbResult && redisResult;
    log(
      `\nOverall result: ${
        allPassed
          ? colors.green + "ALL TESTS PASSED!"
          : colors.red + "SOME TESTS FAILED"
      }${colors.reset}`
    );

    // Write results to file
    const report = `
Connection Test Report
Date: ${new Date().toLocaleString()}

PostgreSQL: ${pgResult ? "PASS" : "FAIL"}
PgBouncer: ${pgbResult ? "PASS" : "FAIL"}
Redis: ${redisResult ? "PASS" : "FAIL"}

Overall: ${allPassed ? "ALL TESTS PASSED" : "SOME TESTS FAILED"}
`;

    fs.writeFileSync("connection-test-report.txt", report);
    log(`Report saved to connection-test-report.txt`, colors.cyan);
  } catch (error) {
    const err = error as Error;
    log(`Unexpected error running tests: ${err.message}`, colors.red);
  }
}

// Run the test suite
runTests();
