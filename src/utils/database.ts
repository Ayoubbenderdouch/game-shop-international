import { Pool, PoolConfig, QueryResult } from "pg";
import dotenv from "dotenv";

dotenv.config();


const poolConfig: PoolConfig = {
  host: process.env.PGBOUNCER_HOST || "pgbouncer",
  port: parseInt(process.env.PGBOUNCER_PORT || "6432", 10),
  user: process.env.PGBOUNCER_USER || "geouser",
  password: process.env.POSTGRES_PASSWORD || "P@ssw0rd_PostgreSQL_Complex!23",
  database: process.env.POSTGRES_DB || "geolocations",
  max: 20,
  idleTimeoutMillis: 30000,
  connectionTimeoutMillis: 3000,
};

const pool = new Pool(poolConfig);

pool.on("error", (err: Error) => {
  console.error("Unexpected error on idle client", err);
});

export const checkDatabaseConnection = async (): Promise<boolean> => {
  try {
    console.log("Attempting to connect to database via PgBouncer...");
    const client = await pool.connect();
    console.log("Client connected, running test query...");
    const result: QueryResult = await client.query(
      "SELECT NOW() as time, current_database() as database, current_user as user"
    );
    console.log("Query result:", result.rows[0]);
    client.release();

    // Handle the case where rowCount could be null
    return result.rowCount !== null && result.rowCount > 0;
  } catch (error: unknown) {
    if (error instanceof Error) {
      console.error("Database connection error:", error.message, error.stack);
      
      // Try alternative database name through PgBouncer
      try {
        console.log("Trying 'postgres' database name through PgBouncer...");
        const altPool = new Pool({
          ...poolConfig,
          database: 'postgres' // Try connecting to 'postgres' which may be mapped to 'geolocations'
        });
        const altClient = await altPool.connect();
        console.log("Connected through PgBouncer using 'postgres' database name");
        const altResult = await altClient.query("SELECT NOW() as time, current_database() as database, current_user as user");
        console.log("PgBouncer alternative query result:", altResult.rows[0]);
        altClient.release();
        await altPool.end();
        return true;
      } catch (altError) {
        if (altError instanceof Error) {
          console.error("PgBouncer 'postgres' database connection failed:", altError.message);
        }
        
        // Try direct PostgreSQL connection as a fallback
        console.log("Trying direct PostgreSQL connection as a fallback...");
        try {
          const directPool = new Pool({
            host: process.env.POSTGRES_HOST || "postgis",
            port: parseInt(process.env.POSTGRES_PORT || "5432", 10),
            user: process.env.POSTGRES_USER || "geouser",
            password: process.env.POSTGRES_PASSWORD || "P@ssw0rd_PostgreSQL_Complex!23",
            database: process.env.POSTGRES_DB || "geolocations",
          });

          const directClient = await directPool.connect();
          console.log("Direct PostgreSQL connection successful!");
          const directResult = await directClient.query("SELECT NOW() as time, current_database() as database, current_user as user");
          console.log("Direct query result:", directResult.rows[0]);
          directClient.release();
          await directPool.end();

          console.error(
            "PgBouncer connections failed but direct PostgreSQL works. Check PgBouncer configuration."
          );
        } catch (directError) {
          if (directError instanceof Error) {
            console.error("Direct PostgreSQL connection failed:", directError.message);
          }
          console.error("All database connection attempts failed");
        }
      }
    } else {
      console.error("Database connection error:", error);
    }
    return false;
  }
};

export default pool;