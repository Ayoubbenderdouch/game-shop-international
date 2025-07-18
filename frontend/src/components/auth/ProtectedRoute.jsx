import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';
import LoadingSpinner from '../common/LoadingSpinner';
import { useEffect, useState } from 'react';
import { supabase } from '../../services/supabase';

const ProtectedRoute = ({ children, adminOnly = false }) => {
  const { user, isAdmin, loading: authLoading } = useAuth();
  const location = useLocation();
  const [isReady, setIsReady] = useState(false);
  const [sessionChecked, setSessionChecked] = useState(false);

  useEffect(() => {
    // Check session before rendering protected content
    const checkSession = async () => {
      try {
        const { data: { session }, error } = await supabase.auth.getSession();
        
        if (!error && session) {
          setSessionChecked(true);
        } else {
          setSessionChecked(true);
        }
      } catch (error) {
        console.error('Session check error:', error);
        setSessionChecked(true);
      }
    };

    checkSession();
  }, []);

  useEffect(() => {
    // Add a small delay to ensure auth state is fully loaded
    if (!authLoading && sessionChecked) {
      const timer = setTimeout(() => {
        setIsReady(true);
      }, 100);
      return () => clearTimeout(timer);
    }
  }, [authLoading, sessionChecked]);

  if (authLoading || !isReady || !sessionChecked) {
    return (
      <div className="flex justify-center items-center min-h-screen">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" state={{ from: location }} replace />;
  }

  if (adminOnly && !isAdmin) {
    return <Navigate to="/" replace />;
  }

  return children;
};

export default ProtectedRoute;