import { motion } from "framer-motion";
import {
  ArrowRight,
  Gamepad2,
  CreditCard,
  Gift,
  Tv,
  Zap,
  Trophy,
  Star,
  Sparkles,
} from "lucide-react";
import { Link } from "react-router-dom";
import { useTranslation } from "react-i18next";
import { useState, useEffect } from "react";
import { productAPI } from "../../services/api";
import ProductCard from "../../components/common/ProductCard";
import LoadingSpinner from "../../components/common/LoadingSpinner";

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
      console.error("Error fetching products:", error);
    } finally {
      setLoading(false);
    }
  };

  const categories = [
    {
      icon: Gamepad2,
      name: "Game Cards",
      slug: "game-cards",
      color: "from-purple-500 to-pink-500",
    },
    {
      icon: CreditCard,
      name: "Gift Cards",
      slug: "gift-cards",
      color: "from-blue-500 to-cyan-500",
    },
    {
      icon: Tv,
      name: "Subscriptions",
      slug: "subscriptions",
      color: "from-green-500 to-emerald-500",
    },
    {
      icon: Gift,
      name: "Game Top-Ups",
      slug: "game-topups",
      color: "from-orange-500 to-red-500",
    },
  ];

  return (
    <div className="space-y-16">
      {/* Enhanced Hero Section */}
      <motion.section
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 0.8 }}
        className="relative overflow-hidden rounded-2xl"
      >
        <div className="absolute inset-0 bg-gradient-to-br from-neon-purple/20 to-neon-blue/20" />

        {/* Animated Background Grid */}
        <div className="absolute inset-0 overflow-hidden">
          <div className="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAgTSAwIDIwIEwgNDAgMjAgTSAyMCAwIEwgMjAgNDAgTSAwIDMwIEwgNDAgMzAgTSAzMCAwIEwgMzAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzIyMiIgb3BhY2l0eT0iMC4yIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-20 animate-pulse" />
        </div>

        <div className="relative z-10 text-center py-24 px-4">
          <motion.div
            initial={{ scale: 0 }}
            animate={{ scale: 1 }}
            transition={{ duration: 0.5 }}
            className="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-full bg-gradient-to-br from-neon-purple to-neon-pink"
          >
            <Zap className="w-10 h-10 text-white" />
          </motion.div>

          <motion.h1
            className="text-5xl md:text-7xl font-bold mb-6"
            initial={{ y: 20, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.2 }}
          >
            <span className="glow-text bg-gradient-to-r from-black via-neon-purple to-neon-pink bg-clip-text text-transparent">
              RELOAD X
            </span>
          </motion.h1>

          <motion.p
            className="text-xl text-gray-300 mb-8 max-w-2xl mx-auto"
            initial={{ y: 20, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.4 }}
          >
            {t("home.hero.subtitle")}
          </motion.p>

          <motion.div
            initial={{ y: 20, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.6 }}
            className="flex flex-col sm:flex-row gap-4 justify-center"
          >
            <Link
              to="/shop"
              className="neon-button inline-flex items-center space-x-2 group"
            >
              <span>{t("home.hero.cta")}</span>
              <ArrowRight className="w-5 h-5 transform group-hover:translate-x-1 transition-transform" />
            </Link>
            <Link
              to="/shop?sort=popular"
              className="px-6 py-3 border-2 border-neon-purple rounded-lg hover:bg-neon-purple/10 transition-all inline-flex items-center space-x-2"
            >
              <Trophy className="w-5 h-5" />
              <span>Best Sellers</span>
            </Link>
          </motion.div>
        </div>

        {/* Floating Gaming Icons Animation */}
        <div className="absolute inset-0 overflow-hidden pointer-events-none">
          {[Gamepad2, Gift, Star, Sparkles].map((Icon, i) => (
            <motion.div
              key={i}
              className="absolute"
              initial={{
                x: Math.random() * window.innerWidth,
                y: window.innerHeight + 100,
                rotate: 0,
              }}
              animate={{
                y: -100,
                rotate: 360,
              }}
              transition={{
                duration: 15 + i * 5,
                repeat: Infinity,
                delay: i * 3,
                ease: "linear",
              }}
            >
              <Icon className="w-8 h-8 text-neon-purple/30" />
            </motion.div>
          ))}
        </div>
      </motion.section>

      {/* Stats Section */}
      <motion.section
        initial={{ opacity: 0, y: 20 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true }}
        className="grid grid-cols-2 md:grid-cols-4 gap-6"
      >
        {[
          { number: "50K+", label: "Happy Gamers" },
          { number: "100+", label: "Game Titles" },
          { number: "24/7", label: "Instant Delivery" },
          { number: "30+", label: "Countries" },
        ].map((stat, index) => (
          <motion.div
            key={index}
            initial={{ scale: 0 }}
            whileInView={{ scale: 1 }}
            viewport={{ once: true }}
            transition={{ delay: index * 0.1 }}
            className="neon-card text-center p-6"
          >
            <h3 className="text-3xl font-bold text-neon-purple mb-2">
              {stat.number}
            </h3>
            <p className="text-gray-400">{stat.label}</p>
          </motion.div>
        ))}
      </motion.section>

      {/* Enhanced Categories */}
      <section>
        <motion.h2
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-3xl font-bold mb-8 text-center glow-text"
        >
          {t("home.featured")}
        </motion.h2>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
          {categories.map((category, index) => (
            <motion.div
              key={category.slug}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: index * 0.1 }}
            >
              <Link to={`/shop?category=${category.slug}`}>
                <motion.div
                  className="neon-card text-center p-6 cursor-pointer group relative overflow-hidden"
                  whileHover={{ scale: 1.05 }}
                  whileTap={{ scale: 0.95 }}
                >
                  <div
                    className="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-300"
                    style={{
                      backgroundImage: `linear-gradient(to bottom right, var(--tw-gradient-stops))`,
                    }}
                  />

                  <motion.div
                    className={`w-16 h-16 mx-auto mb-4 rounded-lg bg-gradient-to-br ${category.color} flex items-center justify-center transform transition-all group-hover:rotate-12 group-hover:scale-110`}
                  >
                    <category.icon className="w-8 h-8 text-white" />
                  </motion.div>
                  <h3 className="font-semibold group-hover:text-neon-purple transition-colors">
                    {category.name}
                  </h3>
                </motion.div>
              </Link>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Featured Products with Enhanced Design */}
      <section>
        <div className="flex items-center justify-between mb-8">
          <motion.h2
            initial={{ opacity: 0, x: -20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            className="text-3xl font-bold glow-text"
          >
            🔥 Hot Deals
          </motion.h2>
          <motion.div
            initial={{ opacity: 0, x: 20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
          >
            <Link
              to="/shop"
              className="text-neon-purple hover:text-neon-pink transition-colors flex items-center space-x-1 group"
            >
              <span>View All</span>
              <ArrowRight className="w-4 h-4 transform group-hover:translate-x-1 transition-transform" />
            </Link>
          </motion.div>
        </div>

        {loading ? (
          <LoadingSpinner size="lg" />
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {featuredProducts.map((product, index) => (
              <motion.div
                key={product.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: index * 0.1 }}
              >
                <ProductCard product={product} />
              </motion.div>
            ))}
          </div>
        )}
      </section>

      {/* Call to Action Section */}
      <motion.section
        initial={{ opacity: 0, y: 20 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true }}
        className="neon-card text-center py-12 relative overflow-hidden"
      >
        <div className="absolute inset-0 bg-gradient-to-r from-neon-purple/10 to-neon-pink/10 animate-pulse" />
        <div className="relative z-10">
          <h2 className="text-3xl font-bold mb-4">Ready to Level Up?</h2>
          <p className="text-gray-400 mb-8 max-w-2xl mx-auto">
            Join thousands of gamers who trust Reload X for their gaming needs.
            Instant delivery, secure payments, and 24/7 support.
          </p>
          <Link
            to="/register"
            className="neon-button inline-flex items-center space-x-2"
          >
            <span>Get Started Now</span>
            <Sparkles className="w-5 h-5" />
          </Link>
        </div>
      </motion.section>
    </div>
  );
};

export default HomePage;
