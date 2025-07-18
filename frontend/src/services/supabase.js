import { createClient } from "@supabase/supabase-js";
import { SUPABASE_URL, SUPABASE_ANON_KEY } from "../config/api";

// Ensure we only create one instance of the Supabase client
let supabaseInstance = null;

const getSupabaseClient = () => {
  if (!supabaseInstance) {
    supabaseInstance = createClient(SUPABASE_URL, SUPABASE_ANON_KEY, {
      auth: {
        autoRefreshToken: true,
        persistSession: true,
        detectSessionInUrl: true,
        storage: window.localStorage,
        storageKey: "supabase.auth.token",
        flowType: "pkce",
      },
      global: {
        headers: {
          "x-application-name": "gameshop-frontend",
        },
      },
    });
  }
  return supabaseInstance;
};

// Export the singleton instance
export const supabase = getSupabaseClient();

// Helper to wait for session to be ready
export const waitForSession = async (maxAttempts = 20, delay = 100) => {
  console.log("Waiting for session...");

  for (let i = 0; i < maxAttempts; i++) {
    try {
      const {
        data: { session },
        error,
      } = await supabase.auth.getSession();

      if (error) {
        console.error("Error getting session in waitForSession:", error);
        return null;
      }

      if (session) {
        console.log("Session found after", i + 1, "attempts");
        return session;
      }

      // Also check if there's a session in localStorage that Supabase hasn't loaded yet
      const storedSession = localStorage.getItem("supabase.auth.token");
      if (storedSession) {
        try {
          const parsed = JSON.parse(storedSession);
          if (parsed && parsed.access_token) {
            console.log(
              "Found session in localStorage, waiting for Supabase to load it..."
            );
            // Give Supabase more time to load the session
            await new Promise((resolve) => setTimeout(resolve, delay * 2));
            continue;
          }
        } catch (e) {
          console.error("Error parsing stored session:", e);
        }
      }

      if (i < maxAttempts - 1) {
        await new Promise((resolve) => setTimeout(resolve, delay));
      }
    } catch (error) {
      console.error("Error in waitForSession:", error);
      return null;
    }
  }

  console.log("No session found after", maxAttempts, "attempts");
  return null;
};

// Helper functions
export const getSession = async () => {
  try {
    const {
      data: { session },
      error,
    } = await supabase.auth.getSession();
    if (error) {
      console.error("Error getting session:", error);
      return null;
    }
    return session;
  } catch (error) {
    console.error("Session error:", error);
    return null;
  }
};

export const getUser = async () => {
  try {
    const {
      data: { user },
      error,
    } = await supabase.auth.getUser();
    if (error) {
      console.error("Error getting user:", error);
      return null;
    }
    return user;
  } catch (error) {
    console.error("User error:", error);
    return null;
  }
};

// Refresh session helper with retry logic
export const refreshSession = async () => {
  try {
    console.log("Refreshing session...");
    const {
      data: { session },
      error,
    } = await supabase.auth.refreshSession();
    if (error) {
      console.error("Error refreshing session:", error);
      return null;
    }
    console.log("Session refreshed successfully");
    return session;
  } catch (error) {
    console.error("Refresh error:", error);
    return null;
  }
};

// Check if session needs refresh
export const sessionNeedsRefresh = (session) => {
  if (!session) return false;

  // Check if token expires in less than 5 minutes
  const expiresAt = session.expires_at ? session.expires_at * 1000 : 0;
  const fiveMinutesFromNow = Date.now() + 5 * 60 * 1000;

  return expiresAt < fiveMinutesFromNow;
};

// Add session refresh interval with error handling
let refreshInterval;
let refreshTimeout;

export const startSessionRefresh = () => {
  console.log("Starting session refresh manager");

  // Clear any existing intervals
  stopSessionRefresh();

  // Function to check and refresh if needed
  const checkAndRefresh = async () => {
    const session = await getSession();
    if (session && sessionNeedsRefresh(session)) {
      console.log("Session needs refresh, refreshing now...");
      const refreshed = await refreshSession();
      if (!refreshed) {
        console.error("Failed to refresh session");
        stopSessionRefresh();
        return;
      }
    }
  };

  // Initial check
  checkAndRefresh();

  // Set up periodic checks every 4 minutes
  refreshInterval = setInterval(checkAndRefresh, 4 * 60 * 1000);

  // Also set up a timeout to refresh 1 minute before expiry
  const setupExpiryTimeout = async () => {
    const session = await getSession();
    if (session && session.expires_at) {
      const expiresAt = session.expires_at * 1000;
      const now = Date.now();
      const timeUntilExpiry = expiresAt - now - 60 * 1000; // 1 minute before expiry

      if (timeUntilExpiry > 0) {
        console.log(
          `Setting up refresh timeout for ${Math.round(
            timeUntilExpiry / 1000
          )} seconds from now`
        );
        refreshTimeout = setTimeout(async () => {
          console.log("Refreshing session before expiry...");
          await refreshSession();
          setupExpiryTimeout(); // Set up the next timeout
        }, timeUntilExpiry);
      }
    }
  };

  setupExpiryTimeout();
};

export const stopSessionRefresh = () => {
  console.log("Stopping session refresh manager");
  if (refreshInterval) {
    clearInterval(refreshInterval);
    refreshInterval = null;
  }
  if (refreshTimeout) {
    clearTimeout(refreshTimeout);
    refreshTimeout = null;
  }
};
