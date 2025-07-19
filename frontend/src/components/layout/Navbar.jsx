import { Link, useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import { ShoppingCart, User, LogOut, Shield, Menu, X, Zap } from "lucide-react";
import { useState } from "react";
import { useTranslation } from "react-i18next";
import useStore from "../../store/useStore";
import { useAuth } from "../../hooks/useAuth";
import LanguageSwitcher from "../common/LanguageSwitcher";

const Navbar = () => {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { getCartCount } = useStore();
  const { user, isAdmin, logout } = useAuth();
  const cartCount = getCartCount();

  const handleLogout = async () => {
    await logout();
    navigate("/");
  };

  return (
    <nav className="bg-slate-900/95 backdrop-blur-md border-b border-slate-800 sticky top-0 z-50">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-20">
          {/* Logo */}
          <Link to="/" className="flex items-center gap-3">
            <motion.div
              whileHover={{ rotate: 360 }}
              transition={{ duration: 0.5 }}
              className="w-10 h-10 bg-gradient-to-br from-[#49baee] to-[#38a8dc] rounded-xl flex items-center justify-center shadow-[0_0_20px_rgba(73,186,238,0.4)]"
            >
              <Zap className="w-6 h-6 text-slate-950" />
            </motion.div>
            <motion.div
              className="text-2xl font-black"
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
            >
              <span className="text-white">RELOAD</span>
              <span className="text-[#49baee] ml-2">X</span>
            </motion.div>
          </Link>

          {/* Desktop Menu */}
          <div className="hidden md:flex items-center gap-8">
            <Link 
              to="/" 
              className="font-medium text-slate-300 hover:text-[#49baee] transition-colors relative group"
            >
              {t("nav.home")}
              <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#49baee] transition-all duration-300 group-hover:w-full" />
            </Link>
            <Link
              to="/shop"
              className="font-medium text-slate-300 hover:text-[#49baee] transition-colors relative group"
            >
              {t("nav.shop")}
              <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#49baee] transition-all duration-300 group-hover:w-full" />
            </Link>
            {user && (
              <Link
                to="/orders"
                className="font-medium text-slate-300 hover:text-[#49baee] transition-colors relative group"
              >
                {t("nav.orders")}
                <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#49baee] transition-all duration-300 group-hover:w-full" />
              </Link>
            )}
            {isAdmin && (
              <Link
                to="/admin"
                className="font-medium text-slate-300 hover:text-[#49baee] transition-colors flex items-center gap-1 relative group"
              >
                <Shield className="w-4 h-4" />
                {t("nav.admin")}
                <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#49baee] transition-all duration-300 group-hover:w-full" />
              </Link>
            )}
          </div>

          {/* Right Menu */}
          <div className="hidden md:flex items-center gap-6">
            {/* Language Switcher */}
            <LanguageSwitcher />

            <Link
              to="/cart"
              className="relative p-2 text-slate-300 hover:text-[#49baee] transition-colors group"
            >
              <ShoppingCart className="w-6 h-6" />
              {cartCount > 0 && (
                <motion.span
                  className="absolute -top-1 -right-1 bg-[#49baee] text-slate-950 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  transition={{ type: "spring", stiffness: 500 }}
                >
                  {cartCount}
                </motion.span>
              )}
              <span className="absolute inset-0 rounded-lg bg-[#49baee]/10 scale-0 group-hover:scale-100 transition-transform" />
            </Link>

            {user ? (
              <div className="flex items-center gap-4">
                <span className="text-sm text-slate-400">{user.email}</span>
                <button
                  onClick={handleLogout}
                  className="p-2 text-slate-300 hover:text-[#49baee] transition-colors rounded-lg hover:bg-[#49baee]/10"
                >
                  <LogOut className="w-5 h-5" />
                </button>
              </div>
            ) : (
              <Link 
                to="/login" 
                className="px-6 py-2.5 bg-[#49baee] text-slate-950 font-bold rounded-lg hover:bg-[#5cc5f5] hover:shadow-[0_0_20px_rgba(73,186,238,0.5)] transition-all duration-300"
              >
                {t("nav.login")}
              </Link>
            )}
          </div>

          {/* Mobile Menu Button */}
          <button
            className="md:hidden p-2 text-slate-300 hover:text-[#49baee] transition-colors"
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
          >
            {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>

        {/* Mobile Menu */}
        {mobileMenuOpen && (
          <motion.div
            className="md:hidden py-6 space-y-4 border-t border-slate-800"
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
          >
            {/* Language Switcher for Mobile */}
            <div className="pb-4 border-b border-slate-800">
              <LanguageSwitcher />
            </div>

            <Link
              to="/"
              className="block py-2 text-slate-300 hover:text-[#49baee] transition-colors"
              onClick={() => setMobileMenuOpen(false)}
            >
              {t("nav.home")}
            </Link>
            <Link
              to="/shop"
              className="block py-2 text-slate-300 hover:text-[#49baee] transition-colors"
              onClick={() => setMobileMenuOpen(false)}
            >
              {t("nav.shop")}
            </Link>
            <Link
              to="/cart"
              className="block py-2 text-slate-300 hover:text-[#49baee] transition-colors"
              onClick={() => setMobileMenuOpen(false)}
            >
              {t("nav.cart")} ({cartCount})
            </Link>
            {user && (
              <Link
                to="/orders"
                className="block py-2 text-slate-300 hover:text-[#49baee] transition-colors"
                onClick={() => setMobileMenuOpen(false)}
              >
                {t("nav.orders")}
              </Link>
            )}
            {isAdmin && (
              <Link
                to="/admin"
                className="block py-2 text-slate-300 hover:text-[#49baee] transition-colors"
                onClick={() => setMobileMenuOpen(false)}
              >
                {t("nav.admin")}
              </Link>
            )}
            
            <div className="pt-4 border-t border-slate-800">
              {user ? (
                <button
                  onClick={() => {
                    handleLogout();
                    setMobileMenuOpen(false);
                  }}
                  className="block w-full text-left py-2 text-slate-300 hover:text-[#49baee] transition-colors"
                >
                  {t("nav.logout")}
                </button>
              ) : (
                <Link
                  to="/login"
                  className="block w-full text-center px-6 py-3 bg-[#49baee] text-slate-950 font-bold rounded-lg hover:bg-[#5cc5f5] transition-colors"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  {t("nav.login")}
                </Link>
              )}
            </div>
          </motion.div>
        )}
      </div>
    </nav>
  );
};

export default Navbar;