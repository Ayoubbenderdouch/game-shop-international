import { useEffect, useState } from 'react';
import { supabase } from '../services/supabase';
import { authAPI } from '../services/api';
import useStore from '../store/useStore';
import toast from 'react-hot-toast';

export const useAuth = () => {
  const [loading, setLoading] = useState(true);
  const { user, setUser, clearUser, isAdmin } = useStore();

  useEffect(() => {
    checkUser();
    
    const { data: authListener } = supabase.auth.onAuthStateChange(async (event, session) => {
      if (session?.user) {
        await fetchUserProfile();
      } else {
        clearUser();
      }
      setLoading(false);
    });

    return () => {
      authListener?.subscription.unsubscribe();
    };
  }, []);

  const checkUser = async () => {
    try {
      const session = await supabase.auth.getSession();
      if (session?.data?.session?.user) {
        await fetchUserProfile();
      }
    } catch (error) {
      console.error('Error checking user:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchUserProfile = async () => {
    try {
      const { data } = await authAPI.getProfile();
      setUser(data);
    } catch (error) {
      console.error('Error fetching profile:', error);
    }
  };

  const login = async (email, password) => {
    try {
      setLoading(true);
      const { data } = await authAPI.login({ email, password });
      setUser(data.user);
      toast.success('Welcome back!');
      return { success: true };
    } catch (error) {
      return { success: false, error: error.response?.data?.error || 'Login failed' };
    } finally {
      setLoading(false);
    }
  };

  const register = async (email, password) => {
    try {
      setLoading(true);
      const { data } = await authAPI.register({ email, password });
      setUser(data.user);
      toast.success('Account created successfully!');
      return { success: true };
    } catch (error) {
      return { success: false, error: error.response?.data?.error || 'Registration failed' };
    } finally {
      setLoading(false);
    }
  };

  const logout = async () => {
    try {
      await authAPI.logout();
      await supabase.auth.signOut();
      clearUser();
      toast.success('Logged out successfully');
    } catch (error) {
      console.error('Logout error:', error);
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