import { useEffect, useState, useCallback } from "react";
import {
  supabase,
  startSessionRefresh,
  stopSessionRefresh,
} from "../services/supabase";
import { authAPI } from "../services/api";
import useStore from "../store/useStore";
import toast from "react-hot-toast";

export const useAuth = () => {
  const [loading, setLoading] = useState(true);
  const { user, setUser, clearUser, isAdmin } = useStore();

  const fetchUserProfile = useCallback(async () => {
    try {
      const { data } = await authAPI.getProfile();
      setUser(data);
      return true;
    } catch (error) {
      console.error("Error fetching profile:", error);
      // Don't clear user here - let the error interceptor handle it
      return false;
    }
  }, [setUser]);

  const checkUser = useCallback(async () => {
    try {
      const {
        data: { session },
      } = await supabase.auth.getSession();
      if (session?.user) {
        await fetchUserProfile();
        startSessionRefresh(); // Start session refresh
      } else {
        clearUser();
        stopSessionRefresh(); // Stop session refresh
      }
    } catch (error) {
      console.error("Error checking user:", error);
      clearUser();
      stopSessionRefresh();
    } finally {
      setLoading(false);
    }
  }, [fetchUserProfile, clearUser]);

  useEffect(() => {
    // Initial check
    checkUser();

    // Listen for auth changes
    const {
      data: { subscription },
    } = supabase.auth.onAuthStateChange(async (event, session) => {
      console.log("Auth state changed:", event);

      // Skip handling during email verification (when on /auth/confirm path)
      if (
        window.location.pathname.includes("/auth/confirm") &&
        (event === "SIGNED_IN" || event === "INITIAL_SESSION")
      ) {
        console.log("Skipping auth state change during email verification");
        return;
      }

      if (event === "SIGNED_IN" && session?.user) {
        // User signed in - only fetch profile if email is confirmed
        if (session.user.confirmed_at) {
          await fetchUserProfile();
          startSessionRefresh();
        }
      } else if (event === "SIGNED_OUT") {
        // User signed out
        clearUser();
        stopSessionRefresh();
      } else if (event === "TOKEN_REFRESHED" && session?.user) {
        // Token was refreshed, update profile if needed
        if (session.user.confirmed_at) {
          await fetchUserProfile();
        }
      } else if (event === "USER_UPDATED" && session?.user) {
        // User data was updated
        if (session.user.confirmed_at) {
          await fetchUserProfile();
        }
      }

      setLoading(false);
    });

    return () => {
      subscription?.unsubscribe();
      stopSessionRefresh();
    };
  }, [checkUser, fetchUserProfile, clearUser]);

  const login = async (email, password) => {
    try {
      setLoading(true);

      // Clear any existing session before login
      await supabase.auth.signOut();
      clearUser();

      // First, sign in with Supabase
      const { data: supabaseData, error: supabaseError } =
        await supabase.auth.signInWithPassword({
          email,
          password,
        });

      if (supabaseError) {
        // Make sure to clean up on Supabase error
        await supabase.auth.signOut();
        clearUser();
        return { success: false, error: supabaseError.message };
      }

      try {
        // Then call our backend login endpoint
        const { data } = await authAPI.login({ email, password });
        setUser(data.user);

        // Start session refresh after successful login
        startSessionRefresh();

        toast.success("Welcome back!");

        return { success: true };
      } catch (backendError) {
        // If backend login fails, clean up Supabase session
        await supabase.auth.signOut();
        clearUser();

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
      clearUser();

      return {
        success: false,
        error: "Login failed",
      };
    } finally {
      setLoading(false);
    }
  };

  const register = async (email, password) => {
    try {
      setLoading(true);

      // Call our backend register endpoint (which handles Supabase signup)
      const { data } = await authAPI.register({ email, password });
      setUser(data.user);
      toast.success("Account created successfully!");

      return { success: true };
    } catch (error) {
      console.error("Registration error:", error);
      return {
        success: false,
        error: error.response?.data?.error || "Registration failed",
      };
    } finally {
      setLoading(false);
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
      clearUser();

      // Stop session refresh
      stopSessionRefresh();

      toast.success("Logged out successfully");
    } catch (error) {
      console.error("Logout error:", error);
      toast.error("Error logging out");
    } finally {
      setLoading(false);
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
