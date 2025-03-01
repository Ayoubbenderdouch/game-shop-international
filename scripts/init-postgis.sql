-- Enable PostGIS extension
CREATE EXTENSION IF NOT EXISTS postgis;
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Try to create TimescaleDB extension if available
DO $$
BEGIN
  -- Try to create TimescaleDB extension if available
  BEGIN
    CREATE EXTENSION IF NOT EXISTS timescaledb;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'TimescaleDB extension is not available, continuing without it';
  END;
END
$$;

-- Create schema
CREATE SCHEMA IF NOT EXISTS geo;

-- Create driver_locations table for real-time tracking
CREATE TABLE IF NOT EXISTS geo.driver_locations (
  id BIGSERIAL PRIMARY KEY,
  driver_id UUID NOT NULL,
  location GEOMETRY(POINT, 4326) NOT NULL,
  heading FLOAT,
  speed FLOAT,
  accuracy FLOAT,
  status VARCHAR(20) NOT NULL DEFAULT 'online',
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Create a unique constraint to ensure only one row per driver
CREATE UNIQUE INDEX IF NOT EXISTS idx_driver_locations_driver_id_unique
ON geo.driver_locations(driver_id);

-- Create time-partitioned table for historical location data
CREATE TABLE IF NOT EXISTS geo.location_history (
  id BIGSERIAL PRIMARY KEY,
  user_id UUID NOT NULL,
  user_type VARCHAR(10) NOT NULL,
  location GEOMETRY(POINT, 4326) NOT NULL,
  heading FLOAT,
  speed FLOAT,
  accuracy FLOAT,
  timestamp TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Only convert to TimescaleDB hypertable if extension is available
DO $$
BEGIN
  -- Check if TimescaleDB extension exists
  IF EXISTS (SELECT 1 FROM pg_extension WHERE extname = 'timescaledb') THEN
    -- Execute the create_hypertable function
    PERFORM create_hypertable('geo.location_history', 'timestamp', if_not_exists => TRUE);
  ELSE
    RAISE NOTICE 'TimescaleDB not available, skipping hypertable creation';
  END IF;
END
$$;

-- Create GiST index for spatial queries (critical for performance)
CREATE INDEX IF NOT EXISTS idx_driver_locations_location ON geo.driver_locations USING GIST(location);
CREATE INDEX IF NOT EXISTS idx_location_history_location ON geo.location_history USING GIST(location);

-- Create driver-specific index
CREATE INDEX IF NOT EXISTS idx_driver_locations_driver_id ON geo.driver_locations(driver_id);
CREATE INDEX IF NOT EXISTS idx_location_history_user_id ON geo.location_history(user_id);
CREATE INDEX IF NOT EXISTS idx_location_history_timestamp ON geo.location_history(timestamp DESC);

-- Create a function to update driver location
CREATE OR REPLACE FUNCTION geo.update_driver_location(
  p_driver_id UUID,
  p_longitude FLOAT,
  p_latitude FLOAT,
  p_heading FLOAT,
  p_speed FLOAT,
  p_accuracy FLOAT,
  p_status VARCHAR
) RETURNS VOID AS $$
BEGIN
  -- Insert into driver_locations table (or update if exists)
  INSERT INTO geo.driver_locations (driver_id, location, heading, speed, accuracy, status, created_at)
  VALUES (
    p_driver_id,
    ST_SetSRID(ST_MakePoint(p_longitude, p_latitude), 4326),
    p_heading,
    p_speed,
    p_accuracy,
    p_status,
    NOW()
  )
  ON CONFLICT (driver_id)
  DO UPDATE SET
    location = ST_SetSRID(ST_MakePoint(p_longitude, p_latitude), 4326),
    heading = p_heading,
    speed = p_speed,
    accuracy = p_accuracy,
    status = p_status,
    created_at = NOW();

  -- Insert into location_history table for historical tracking
  INSERT INTO geo.location_history (user_id, user_type, location, heading, speed, accuracy, timestamp)
  VALUES (
    p_driver_id,
    'driver',
    ST_SetSRID(ST_MakePoint(p_longitude, p_latitude), 4326),
    p_heading,
    p_speed,
    p_accuracy,
    NOW()
  );
END;
$$ LANGUAGE plpgsql;

-- Create a function to find nearby drivers
CREATE OR REPLACE FUNCTION geo.find_nearby_drivers(
  p_longitude FLOAT,
  p_latitude FLOAT,
  p_radius_meters INT DEFAULT 5000,
  p_limit INT DEFAULT 50
) RETURNS TABLE (
  driver_id UUID,
  distance_meters FLOAT,
  heading FLOAT,
  speed FLOAT,
  status VARCHAR,
  last_updated TIMESTAMPTZ
) AS $$
BEGIN
  RETURN QUERY
  SELECT
    dl.driver_id,
    ST_Distance(
      dl.location::geography,
      ST_SetSRID(ST_MakePoint(p_longitude, p_latitude), 4326)::geography
    ) AS distance_meters,
    dl.heading,
    dl.speed,
    dl.status,
    dl.created_at AS last_updated
  FROM
    geo.driver_locations dl
  WHERE
    dl.status = 'online'
    AND ST_DWithin(
      dl.location::geography,
      ST_SetSRID(ST_MakePoint(p_longitude, p_latitude), 4326)::geography,
      p_radius_meters
    )
    AND dl.created_at > NOW() - INTERVAL '5 minutes'
  ORDER BY
    distance_meters ASC
  LIMIT p_limit;
END;
$$ LANGUAGE plpgsql;

-- Grant privileges
GRANT ALL ON SCHEMA geo TO geouser;
GRANT ALL ON ALL TABLES IN SCHEMA geo TO geouser;
GRANT ALL ON ALL SEQUENCES IN SCHEMA geo TO geouser;
GRANT EXECUTE ON ALL FUNCTIONS IN SCHEMA geo TO geouser;