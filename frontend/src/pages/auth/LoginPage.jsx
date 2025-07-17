import { useState } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Mail, Lock, LogIn, AlertCircle } from 'lucide-react';
import { useForm } from 'react-hook-form';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../../hooks/useAuth';

const LoginPage = () => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const location = useLocation();
  const { login } = useAuth();
  const [loading, setLoading] = useState(false);
  const [loginError, setLoginError] = useState('');
  
  const { register, handleSubmit, formState: { errors } } = useForm();
  const from = location.state?.from?.pathname || '/';

  const onSubmit = async (data) => {
    try {
      setLoading(true);
      setLoginError(''); // Clear any previous errors
      
      const result = await login(data.email, data.password);
      
      if (result.success) {
        // Small delay to ensure auth state is updated
        setTimeout(() => {
          navigate(from, { replace: true });
        }, 100);
      } else {
        // Show the error message
        setLoginError(result.error || 'Login failed. Please check your credentials.');
      }
    } catch (error) {
      console.error('Login error:', error);
      setLoginError('An unexpected error occurred. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-md mx-auto">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="neon-card"
      >
        <div className="text-center mb-8">
          <LogIn className="w-12 h-12 mx-auto mb-4 text-neon-purple" />
          <h1 className="text-3xl font-bold glow-text">{t('auth.login.title')}</h1>
        </div>

        {/* Show login error if any */}
        {loginError && (
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            animate={{ opacity: 1, y: 0 }}
            className="mb-6 p-4 bg-red-500/10 border border-red-500 rounded-lg flex items-center gap-3"
          >
            <AlertCircle className="w-5 h-5 text-red-500 flex-shrink-0" />
            <p className="text-sm text-red-500">{loginError}</p>
          </motion.div>
        )}

        <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
          <div>
            <label className="block text-sm font-medium mb-2">
              {t('auth.login.email')}
            </label>
            <div className="relative">
              <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="email"
                {...register('email', {
                  required: 'Email is required',
                  pattern: {
                    value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                    message: 'Invalid email address'
                  }
                })}
                className="w-full pl-10 pr-4 py-3 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                placeholder="you@example.com"
              />
            </div>
            {errors.email && (
              <p className="mt-1 text-sm text-red-500">{errors.email.message}</p>
            )}
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">
              {t('auth.login.password')}
            </label>
            <div className="relative">
              <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="password"
                {...register('password', {
                  required: 'Password is required',
                  minLength: {
                    value: 6,
                    message: 'Password must be at least 6 characters'
                  }
                })}
                className="w-full pl-10 pr-4 py-3 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                placeholder="••••••••"
              />
            </div>
            {errors.password && (
              <p className="mt-1 text-sm text-red-500">{errors.password.message}</p>
            )}
          </div>

          <motion.button
            type="submit"
            disabled={loading}
            className="w-full neon-button disabled:opacity-50 disabled:cursor-not-allowed"
            whileHover={!loading ? { scale: 1.02 } : {}}
            whileTap={!loading ? { scale: 0.98 } : {}}
          >
            {loading ? 'Logging in...' : t('auth.login.submit')}
          </motion.button>
        </form>

        <div className="mt-6 text-center text-sm">
          <span className="text-gray-400">{t('auth.login.noAccount')} </span>
          <Link to="/register" className="text-neon-purple hover:text-neon-pink transition-colors">
            {t('auth.login.signUp')}
          </Link>
        </div>
      </motion.div>
    </div>
  );
};

export default LoginPage;