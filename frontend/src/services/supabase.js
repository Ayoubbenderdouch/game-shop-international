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
    flowType: 'pkce'
  }
});

// Helper functions
export const getSession = async () => {
  const { data: { session }, error } = await supabase.auth.getSession();
  if (error) {
    console.error('Error getting session:', error);
    return null;
  }
  return session;
};

export const getUser = async () => {
  const { data: { user }, error } = await supabase.auth.getUser();
  if (error) {
    console.error('Error getting user:', error);
    return null;
  }
  return user;
};

// Refresh session helper
export const refreshSession = async () => {
  const { data: { session }, error } = await supabase.auth.refreshSession();
  if (error) {
    console.error('Error refreshing session:', error);
    return null;
  }
  return session;
};