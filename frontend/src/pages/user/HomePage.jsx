import { motion } from 'framer-motion';
import { ArrowRight, Gamepad2, CreditCard, Gift, Tv } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useState, useEffect } from 'react';
import { productAPI } from '../../services/api';
import ProductCard from '../../components/common/ProductCard';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const HomePage = () => {
  const { t } = useTranslation();
  const [featuredProducts, setFeaturedProducts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchFeaturedProducts();
  }, []);

  const fetchFeaturedProducts = async () => {
    try {
      const { data } = await productAPI.getAll({ limit: 8 });
      setFeaturedProducts(data.products);
    } catch (error) {
      console.error('Error fetching products:', error);
    } finally {
      setLoading(false);
    }
  };

  const categories = [
    { icon: Gamepad2, name: 'Game Cards', slug: 'game-cards', color: 'from-purple-500 to-pink-500' },
    { icon: CreditCard, name: 'Gift Cards', slug: 'gift-cards', color: 'from-blue-500 to-cyan-500' },
    { icon: Tv, name: 'Subscriptions', slug: 'subscriptions', color: 'from-green-500 to-emerald-500' },
    { icon: Gift, name: 'Game Top-Ups', slug: 'game-topups', color: 'from-orange-500 to-red-500' },
  ];

  return (
    <div className="space-y-16">
      {/* Hero Section */}
      <motion.section
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 0.8 }}
        className="relative overflow-hidden rounded-2xl"
      >
        <div className="absolute inset-0 bg-gradient-to-br from-neon-purple/20 to-neon-blue/20" />
        <div className="relative z-10 text-center py-20 px-4">
          <motion.h1
            className="text-5xl md:text-7xl font-bold mb-6"
            initial={{ y: 20, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.2 }}
          >
            <span className="glow-text">{t('home.hero.title')}</span>
          </motion.h1>
          
          <motion.p
            className="text-xl text-gray-300 mb-8 max-w-2xl mx-auto"
            initial={{ y: 20, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.4 }}
          >
            {t('home.hero.subtitle')}
          </motion.p>
          
          <motion.div
            initial={{ y: 20, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.6 }}
          >
            <Link to="/shop" className="neon-button inline-flex items-center space-x-2">
              <span>{t('home.hero.cta')}</span>
              <ArrowRight className="w-5 h-5" />
            </Link>
          </motion.div>
        </div>

        {/* Animated Background Elements */}
        <div className="absolute inset-0 overflow-hidden pointer-events-none">
          {[...Array(5)].map((_, i) => (
            <motion.div
              key={i}
              className="absolute w-64 h-64 bg-neon-purple/10 rounded-full blur-3xl"
              initial={{ x: -300, y: Math.random() * 400 }}
              animate={{
                x: window.innerWidth + 300,
                y: Math.random() * 400,
              }}
              transition={{
                duration: 15 + i * 5,
                repeat: Infinity,
                delay: i * 2,
              }}
            />
          ))}
        </div>
      </motion.section>

      {/* Categories */}
      <section>
        <h2 className="text-3xl font-bold mb-8 text-center glow-text">
          {t('home.featured')}
        </h2>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
          {categories.map((category, index) => (
            <motion.div
              key={category.slug}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: index * 0.1 }}
            >
              <Link to={`/shop?category=${category.slug}`}>
                <motion.div
                  className="neon-card text-center p-6 cursor-pointer group"
                  whileHover={{ scale: 1.05 }}
                  whileTap={{ scale: 0.95 }}
                >
                  <div className={`w-16 h-16 mx-auto mb-4 rounded-lg bg-gradient-to-br ${category.color} flex items-center justify-center transform transition-transform group-hover:rotate-12`}>
                    <category.icon className="w-8 h-8 text-white" />
                  </div>
                  <h3 className="font-semibold">{category.name}</h3>
                </motion.div>
              </Link>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Featured Products */}
      <section>
        <div className="flex items-center justify-between mb-8">
          <h2 className="text-3xl font-bold glow-text">Featured Products</h2>
          <Link to="/shop" className="text-neon-purple hover:text-neon-pink transition-colors flex items-center space-x-1">
            <span>View All</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
        
        {loading ? (
          <LoadingSpinner size="lg" />
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {featuredProducts.map((product, index) => (
              <motion.div
                key={product.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: index * 0.1 }}
              >
                <ProductCard product={product} />
              </motion.div>
            ))}
          </div>
        )}
      </section>
    </div>
  );
};

export default HomePage;