// Add this component temporarily to your App.jsx to debug auth issues
// Place it inside your Router but outside any routes

import { useEffect, useState } from 'react';
import { supabase } from '../services/supabase';
import { useAuth } from '../hooks/useAuth';

const AuthDebug = () => {
  const [sessionInfo, setSessionInfo] = useState(null);
  const [storedToken, setStoredToken] = useState(null);
  const { user, loading } = useAuth();

  useEffect(() => {
    const checkSession = async () => {
      // Check Supabase session
      const { data: { session }, error } = await supabase.auth.getSession();
      setSessionInfo({
        hasSession: !!session,
        userId: session?.user?.id,
        email: session?.user?.email,
        expiresAt: session?.expires_at ? new Date(session.expires_at * 1000).toLocaleString() : null,
        error: error?.message
      });

      // Check localStorage
      const stored = localStorage.getItem('supabase.auth.token');
      if (stored) {
        try {
          const parsed = JSON.parse(stored);
          setStoredToken({
            hasToken: !!parsed.access_token,
            expiresAt: parsed.expires_at ? new Date(parsed.expires_at * 1000).toLocaleString() : null
          });
        } catch (e) {
          setStoredToken({ error: 'Failed to parse stored token' });
        }
      } else {
        setStoredToken({ hasToken: false });
      }
    };

    checkSession();
    
    // Check every second
    const interval = setInterval(checkSession, 1000);
    
    return () => clearInterval(interval);
  }, []);

  if (process.env.NODE_ENV !== 'development') return null;

  return (
    <div style={{
      position: 'fixed',
      bottom: 10,
      right: 10,
      background: 'rgba(0, 0, 0, 0.9)',
      color: 'white',
      padding: '10px',
      borderRadius: '5px',
      fontSize: '12px',
      fontFamily: 'monospace',
      maxWidth: '300px',
      zIndex: 9999
    }}>
      <div><strong>Auth Debug</strong></div>
      <div>Loading: {loading ? 'Yes' : 'No'}</div>
      <div>User: {user ? user.email : 'None'}</div>
      <div>Session: {sessionInfo?.hasSession ? 'Yes' : 'No'}</div>
      <div>Stored Token: {storedToken?.hasToken ? 'Yes' : 'No'}</div>
      {sessionInfo?.expiresAt && (
        <div>Expires: {sessionInfo.expiresAt}</div>
      )}
      {sessionInfo?.error && (
        <div style={{ color: 'red' }}>Error: {sessionInfo.error}</div>
      )}
    </div>
  );
};

export default AuthDebug;