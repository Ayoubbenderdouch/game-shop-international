import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Loader } from 'lucide-react';

const AuthCallbackPage = () => {
  const navigate = useNavigate();

  useEffect(() => {
    // Handle the callback and redirect
    const handleCallback = async () => {
      // Small delay to ensure Supabase processes the callback
      setTimeout(() => {
        navigate('/auth/confirm', { replace: true });
      }, 100);
    };

    handleCallback();
  }, [navigate]);

  return (
    <div className="flex items-center justify-center min-h-screen">
      <div className="text-center">
        <Loader className="w-12 h-12 mx-auto mb-4 text-neon-purple animate-spin" />
        <p className="text-gray-400">Processing...</p>
      </div>
    </div>
  );
};

export default AuthCallbackPage;