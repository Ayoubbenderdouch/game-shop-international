import { createClient } from '@supabase/supabase-js';
import { SUPABASE_URL, SUPABASE_ANON_KEY } from '../config/api';

// Create Supabase client with better configuration
export const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY, {
  auth: {
    autoRefreshToken: true,
    persistSession: true,
    detectSessionInUrl: true,
    storage: window.localStorage,
    storageKey: 'supabase.auth.token',
    flowType: 'pkce',
    // Updated session refresh configuration
    sessionAutoRefresh: true,
  }
});

// Helper functions
export const getSession = async () => {
  try {
    const { data: { session }, error } = await supabase.auth.getSession();
    if (error) {
      console.error('Error getting session:', error);
      return null;
    }
    return session;
  } catch (error) {
    console.error('Session error:', error);
    return null;
  }
};

export const getUser = async () => {
  try {
    const { data: { user }, error } = await supabase.auth.getUser();
    if (error) {
      console.error('Error getting user:', error);
      return null;
    }
    return user;
  } catch (error) {
    console.error('User error:', error);
    return null;
  }
};

// Refresh session helper
export const refreshSession = async () => {
  try {
    const { data: { session }, error } = await supabase.auth.refreshSession();
    if (error) {
      console.error('Error refreshing session:', error);
      return null;
    }
    return session;
  } catch (error) {
    console.error('Refresh error:', error);
    return null;
  }
};

// Add session refresh interval with error handling
let refreshInterval;

export const startSessionRefresh = () => {
  if (refreshInterval) clearInterval(refreshInterval);
  
  // Initial refresh
  refreshSession();
  
  refreshInterval = setInterval(async () => {
    const session = await getSession();
    if (session) {
      const refreshed = await refreshSession();
      if (!refreshed) {
        // If refresh fails, stop the interval
        stopSessionRefresh();
      }
    } else {
      // No session, stop refreshing
      stopSessionRefresh();
    }
  }, 20 * 60 * 1000); // Refresh every 20 minutes
};

export const stopSessionRefresh = () => {
  if (refreshInterval) {
    clearInterval(refreshInterval);
    refreshInterval = null;
  }
};