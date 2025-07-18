import { Link, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { ShoppingCart, User, LogOut, Shield, Menu, X } from 'lucide-react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import useStore from '../../store/useStore';
import { useAuth } from '../../hooks/useAuth';

const Navbar = () => {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { getCartCount } = useStore();
  const { user, isAdmin, logout } = useAuth();
  const cartCount = getCartCount();

  const handleLogout = async () => {
    await logout();
    navigate('/');
  };

  return (
    <nav className="bg-dark-card border-b border-dark-border sticky top-0 z-50">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link to="/" className="flex items-center">
            <motion.img
              src="/favi.png" 
              alt="Reload X" 
              className="h-40 w-40 object-contain"
              whileHover={{ scale: 1.1 }}
              whileTap={{ scale: 0.95 }}
            />
          </Link>

          {/* Desktop Menu */}
          <div className="hidden md:flex items-center space-x-8">
            <Link to="/" className="hover:text-neon-purple transition-colors">
              {t('nav.home')}
            </Link>
            <Link to="/shop" className="hover:text-neon-purple transition-colors">
              {t('nav.shop')}
            </Link>
            {user && (
              <Link to="/orders" className="hover:text-neon-purple transition-colors">
                {t('nav.orders')}
              </Link>
            )}
            {isAdmin && (
              <Link to="/admin" className="hover:text-neon-purple transition-colors flex items-center">
                <Shield className="w-4 h-4 mr-1" />
                {t('nav.admin')}
              </Link>
            )}
          </div>

          {/* Right Menu */}
          <div className="hidden md:flex items-center space-x-4">
            <Link to="/cart" className="relative hover:text-neon-purple transition-colors">
              <ShoppingCart className="w-6 h-6" />
              {cartCount > 0 && (
                <motion.span
                  className="absolute -top-2 -right-2 bg-neon-pink text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  transition={{ type: "spring", stiffness: 500 }}
                >
                  {cartCount}
                </motion.span>
              )}
            </Link>
            
            {user ? (
              <div className="flex items-center space-x-4">
                <span className="text-sm text-gray-400">{user.email}</span>
                <button
                  onClick={handleLogout}
                  className="hover:text-neon-purple transition-colors"
                >
                  <LogOut className="w-5 h-5" />
                </button>
              </div>
            ) : (
              <Link to="/login" className="neon-button text-sm">
                {t('nav.login')}
              </Link>
            )}
          </div>

          {/* Mobile Menu Button */}
          <button
            className="md:hidden"
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
          >
            {mobileMenuOpen ? <X /> : <Menu />}
          </button>
        </div>

        {/* Mobile Menu */}
        {mobileMenuOpen && (
          <motion.div
            className="md:hidden py-4 space-y-4"
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
          >
            <Link to="/" className="block hover:text-neon-purple transition-colors">
              {t('nav.home')}
            </Link>
            <Link to="/shop" className="block hover:text-neon-purple transition-colors">
              {t('nav.shop')}
            </Link>
            <Link to="/cart" className="block hover:text-neon-purple transition-colors">
              {t('nav.cart')} ({cartCount})
            </Link>
            {user && (
              <Link to="/orders" className="block hover:text-neon-purple transition-colors">
                {t('nav.orders')}
              </Link>
            )}
            {isAdmin && (
              <Link to="/admin" className="block hover:text-neon-purple transition-colors">
                {t('nav.admin')}
              </Link>
            )}
            {user ? (
              <button
                onClick={handleLogout}
                className="block w-full text-left hover:text-neon-purple transition-colors"
              >
                {t('nav.logout')}
              </button>
            ) : (
              <Link to="/login" className="block hover:text-neon-purple transition-colors">
                {t('nav.login')}
              </Link>
            )}
          </motion.div>
        )}
      </div>
    </nav>
  );
};

export default Navbar;