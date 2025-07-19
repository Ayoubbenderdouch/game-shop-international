import { useEffect, useState, useCallback, useRef } from "react";
import {
  supabase,
  startSessionRefresh,
  stopSessionRefresh,
  sessionNeedsRefresh,
  refreshSession,
  getSession,
} from "../services/supabase";
import { authAPI } from "../services/api";
import useStore from "../store/useStore";
import toast from "react-hot-toast";

// Global flag to track if auth is already initializing
let isInitializing = false;

export const useAuth = () => {
  const [loading, setLoading] = useState(true);
  const { user, setUser, clearUser, isAdmin } = useStore();
  const mountedRef = useRef(true);
  const initStartedRef = useRef(false);

  const fetchUserProfile = useCallback(async () => {
    try {
      const { data } = await authAPI.getProfile();
      if (mountedRef.current) {
        setUser(data);
      }
      return true;
    } catch (error) {
      console.error("Error fetching profile:", error);
      console.error("Error response:", error.response);
      // Don't clear user here - let the error interceptor handle it
      return false;
    }
  }, [setUser]);

  // Initialize auth on mount
  useEffect(() => {
    mountedRef.current = true;

    // Prevent multiple simultaneous initializations
    if (initStartedRef.current || isInitializing) {
      console.log("Auth initialization already in progress, skipping...");
      return;
    }

    initStartedRef.current = true;
    isInitializing = true;

    const initAuth = async () => {
      try {
        console.log("Initializing auth on mount...");
        
        // Small delay to let Supabase fully initialize
        await new Promise(resolve => setTimeout(resolve, 100));
        
        // Get the current session
        const session = await getSession();
        
        if (!mountedRef.current) {
          console.log("Component unmounted during init");
          return;
        }

        if (session?.user && session.user.confirmed_at) {
          console.log("Found existing session on mount");
          
          // Check if we need to refresh the token first
          if (sessionNeedsRefresh(session)) {
            console.log("Session needs refresh on mount");
            const refreshedSession = await refreshSession();
            if (!refreshedSession) {
              console.error("Failed to refresh session on mount");
              if (mountedRef.current) {
                clearUser();
                setLoading(false);
              }
              return;
            }
          }
          
          // Fetch the user profile
          const success = await fetchUserProfile();
          if (success && mountedRef.current) {
            startSessionRefresh();
          }
        } else {
          console.log("No session found on mount");
          if (mountedRef.current) {
            clearUser();
          }
        }
      } catch (error) {
        console.error("Error during auth initialization:", error);
        if (mountedRef.current) {
          clearUser();
        }
      } finally {
        if (mountedRef.current) {
          setLoading(false);
        }
        isInitializing = false;
      }
    };

    initAuth();

    return () => {
      mountedRef.current = false;
    };
  }, []); // Empty deps - only run once

  // Listen for auth state changes
  useEffect(() => {
    let subscription;

    const setupAuthListener = async () => {
      // Small delay to ensure Supabase is ready
      await new Promise(resolve => setTimeout(resolve, 50));

      const { data } = supabase.auth.onAuthStateChange(async (event, session) => {
        if (!mountedRef.current) return;

        console.log("Auth state changed:", event);

        // Don't handle initial session as we already handle it in initAuth
        if (event === "INITIAL_SESSION") {
          return;
        }

        // Skip during email verification
        if (
          window.location.pathname.includes("/auth/confirm") &&
          window.location.hash.includes("access_token")
        ) {
          console.log("Skipping auth change during email verification");
          return;
        }

        if (event === "SIGNED_IN" && session?.user?.confirmed_at) {
          console.log("User signed in via auth state change");
          
          // Small delay to let things stabilize
          await new Promise(resolve => setTimeout(resolve, 100));
          
          if (mountedRef.current) {
            const success = await fetchUserProfile();
            if (success) {
              startSessionRefresh();
            }
            setLoading(false);
          }
        } else if (event === "SIGNED_OUT") {
          console.log("User signed out");
          if (mountedRef.current) {
            clearUser();
            stopSessionRefresh();
            setLoading(false);
          }
        } else if (event === "TOKEN_REFRESHED") {
          console.log("Token was refreshed by Supabase");
          // No need to do anything - Supabase handles this
        } else if (event === "USER_UPDATED" && session?.user?.confirmed_at) {
          console.log("User data updated");
          if (mountedRef.current) {
            await fetchUserProfile();
          }
        }
      });

      subscription = data.subscription;
    };

    setupAuthListener();

    return () => {
      if (subscription) {
        subscription.unsubscribe();
      }
    };
  }, [fetchUserProfile, clearUser]);

  // Cleanup on unmount
  useEffect(() => {
    return () => {
      if (!mountedRef.current) {
        stopSessionRefresh();
      }
    };
  }, []);

  const login = async (email, password) => {
    try {
      // First, sign in with Supabase
      const { data: supabaseData, error: supabaseError } =
        await supabase.auth.signInWithPassword({
          email,
          password,
        });

      if (supabaseError) {
        return { success: false, error: supabaseError.message };
      }

      try {
        // Then call our backend login endpoint
        const { data } = await authAPI.login({ email, password });
        if (mountedRef.current) {
          setUser(data.user);
        }

        // Start session refresh after successful login
        startSessionRefresh();

        toast.success("Welcome back!");

        return { success: true };
      } catch (backendError) {
        // If backend login fails, clean up Supabase session
        await supabase.auth.signOut();
        if (mountedRef.current) {
          clearUser();
        }

        // Check for specific error types
        const errorMessage =
          backendError.response?.data?.error || "Login failed";
        const errorCode = backendError.response?.data?.code;

        return {
          success: false,
          error: errorMessage,
          code: errorCode,
        };
      }
    } catch (error) {
      console.error("Login error:", error);

      // Ensure cleanup on any error
      await supabase.auth.signOut();
      if (mountedRef.current) {
        clearUser();
      }

      return {
        success: false,
        error: "Login failed",
      };
    }
  };

  const register = async (email, password) => {
    try {
      setLoading(true);

      // Call our backend register endpoint (which handles Supabase signup)
      const { data } = await authAPI.register({ email, password });
      
      // Don't set user here as they need to verify email first
      toast.success("Account created successfully! Please check your email to verify your account.");

      return { success: true };
    } catch (error) {
      console.error("Registration error:", error);
      return {
        success: false,
        error: error.response?.data?.error || "Registration failed",
      };
    } finally {
      if (mountedRef.current) {
        setLoading(false);
      }
    }
  };

  const logout = async () => {
    try {
      setLoading(true);

      // Call backend logout first
      try {
        await authAPI.logout();
      } catch (error) {
        console.error("Backend logout error:", error);
      }

      // Then sign out from Supabase
      await supabase.auth.signOut();

      // Clear local user state
      if (mountedRef.current) {
        clearUser();
      }

      // Stop session refresh
      stopSessionRefresh();

      toast.success("Logged out successfully");
    } catch (error) {
      console.error("Logout error:", error);
      toast.error("Error logging out");
    } finally {
      if (mountedRef.current) {
        setLoading(false);
      }
    }
  };

  return {
    user,
    isAdmin,
    loading,
    login,
    register,
    logout,
    isAuthenticated: !!user,
  };
};