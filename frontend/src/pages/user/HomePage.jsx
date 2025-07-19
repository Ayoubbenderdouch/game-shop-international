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
  const [currentSlide, setCurrentSlide] = useState(0);

  // Carousel images data
  const carouselSlides = [
    {
      id: 1,
      image: "https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1200&h=600&fit=crop",
      title: "Gaming Gift Cards",
      subtitle: "Steam, PlayStation, Xbox & More",
      badge: "BEST SELLERS",
    },
    {
      id: 2,
      image: "https://images.unsplash.com/photo-1556656793-08538906a9f8?w=1200&h=600&fit=crop",
      title: "Streaming Services",
      subtitle: "Netflix, Spotify, Disney+ & More",
      badge: "TRENDING",
    },
    {
      id: 3,
      image: "https://images.unsplash.com/photo-1612287230202-1ff1d85d1bdf?w=1200&h=600&fit=crop",
      title: "Digital Subscriptions",
      subtitle: "Premium Services at Best Prices",
      badge: "HOT DEALS",
    },
  ];

  useEffect(() => {
    fetchFeaturedProducts();
    
    // Auto-slide carousel
    const interval = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % carouselSlides.length);
    }, 5000);
    
    return () => clearInterval(interval);
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

  const nextSlide = () => {
    setCurrentSlide((prev) => (prev + 1) % carouselSlides.length);
  };

  const prevSlide = () => {
    setCurrentSlide((prev) => (prev - 1 + carouselSlides.length) % carouselSlides.length);
  };

  const categories = [
    {
      icon: Gamepad2,
      name: "Game Cards",
      slug: "game-cards",
      color: "from-[#49baee] to-[#38a8dc]",
      description: "Top gaming platforms",
    },
    {
      icon: CreditCard,
      name: "Gift Cards",
      slug: "gift-cards",
      color: "from-[#5cc5f5] to-[#49baee]",
      description: "Popular retailers",
    },
    {
      icon: Tv,
      name: "Subscriptions",
      slug: "subscriptions",
      color: "from-[#38a8dc] to-[#2d8cb8]",
      description: "Streaming & services",
    },
    {
      icon: Gift,
      name: "Game Top-Ups",
      slug: "game-topups",
      color: "from-[#49baee] to-[#38a8dc]",
      description: "In-game currencies",
    },
  ];

  const features = [
    {
      icon: Zap,
      title: "Instant Delivery",
      description: "Get your codes in seconds",
    },
    {
      icon: Star,
      title: "Trusted Service",
      description: "5-star rated platform",
    },
    {
      icon: Trophy,
      title: "Best Prices",
      description: "Competitive rates always",
    },
    {
      icon: Gift,
      title: "Wide Selection",
      description: "1000+ products available",
    },
  ];

  return (
    <div className="space-y-24 -mt-8">
      {/* Hero Section with Carousel */}
      <motion.section
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 0.8 }}
        className="relative min-h-[85vh] flex items-center justify-center overflow-hidden"
      >
        {/* Dynamic Background */}
        <div className="absolute inset-0">
          <div className="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950" />
          <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(73,186,238,0.1),transparent_50%)]" />
          <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(73,186,238,0.05),transparent_50%)]" />
        </div>

        {/* Carousel Container */}
        <div className="relative z-10 w-full max-w-7xl mx-auto px-4">
          <div className="relative h-[600px] flex items-center justify-center">
            {/* Carousel Images */}
            {carouselSlides.map((slide, index) => {
              const isActive = index === currentSlide;
              const isPrev = index === (currentSlide - 1 + carouselSlides.length) % carouselSlides.length;
              const isNext = index === (currentSlide + 1) % carouselSlides.length;
              
              return (
                <motion.div
                  key={slide.id}
                  animate={{
                    x: isActive ? 0 : isPrev ? "-85%" : isNext ? "85%" : 0,
                    scale: isActive ? 1 : 0.8,
                    opacity: isActive ? 1 : isPrev || isNext ? 0.5 : 0,
                    zIndex: isActive ? 10 : 5,
                  }}
                  transition={{ duration: 0.5, ease: "easeInOut" }}
                  className="absolute w-[90%] h-full cursor-pointer"
                  onClick={() => {
                    if (isPrev) prevSlide();
                    if (isNext) nextSlide();
                  }}
                >
                  <div className="relative w-full h-full rounded-3xl overflow-hidden group">
                    {/* Image */}
                    <img
                      src={slide.image}
                      alt={slide.title}
                      className="w-full h-full object-cover"
                    />
                    
                    {/* Gradient Overlay */}
                    <div className="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent" />
                    
                    {/* Content */}
                    <div className="absolute bottom-0 left-0 right-0 p-12">
                      <motion.div
                        initial={{ y: 20, opacity: 0 }}
                        animate={{ y: 0, opacity: isActive ? 1 : 0 }}
                        transition={{ delay: 0.2 }}
                      >
                        <span className="inline-block px-4 py-2 bg-[#49baee] text-slate-950 font-bold rounded-lg text-sm mb-4">
                          {slide.badge}
                        </span>
                        <h2 className="text-5xl font-black text-white mb-2">
                          {slide.title}
                        </h2>
                        <p className="text-xl text-slate-300 mb-6">
                          {slide.subtitle}
                        </p>
                        {isActive && (
                          <Link
                            to="/shop"
                            className="inline-flex items-center gap-2 px-8 py-4 bg-[#49baee] text-slate-950 font-bold rounded-xl hover:bg-[#5cc5f5] hover:shadow-[0_0_30px_rgba(73,186,238,0.5)] transition-all duration-300"
                          >
                            Explore Collection
                            <ArrowRight className="w-5 h-5" />
                          </Link>
                        )}
                      </motion.div>
                    </div>
                    
                    {/* Hover Effect */}
                    {isActive && (
                      <div className="absolute inset-0 bg-gradient-to-t from-[#49baee]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                    )}
                  </div>
                </motion.div>
              );
            })}
            
            {/* Navigation Arrows */}
            <button
              onClick={prevSlide}
              className="absolute left-4 z-20 p-3 bg-slate-900/80 backdrop-blur-sm rounded-full text-white hover:bg-slate-800 transition-colors group"
            >
              <ArrowRight className="w-6 h-6 rotate-180 group-hover:-translate-x-1 transition-transform" />
            </button>
            <button
              onClick={nextSlide}
              className="absolute right-4 z-20 p-3 bg-slate-900/80 backdrop-blur-sm rounded-full text-white hover:bg-slate-800 transition-colors group"
            >
              <ArrowRight className="w-6 h-6 group-hover:translate-x-1 transition-transform" />
            </button>
            
            {/* Dots Indicator */}
            <div className="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
              {carouselSlides.map((_, index) => (
                <button
                  key={index}
                  onClick={() => setCurrentSlide(index)}
                  className={`w-2 h-2 rounded-full transition-all duration-300 ${
                    index === currentSlide
                      ? "w-8 bg-[#49baee]"
                      : "bg-slate-600 hover:bg-slate-500"
                  }`}
                />
              ))}
            </div>
          </div>
          
          {/* Trust Badges */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.8 }}
            className="mt-12 flex flex-wrap items-center justify-center gap-8 text-slate-400"
          >
            {features.map((feature, index) => (
              <div key={index} className="flex items-center gap-2">
                <feature.icon className="w-5 h-5 text-[#49baee]" />
                <span className="text-sm font-medium">{feature.title}</span>
              </div>
            ))}
          </motion.div>
        </div>
      </motion.section>

      {/* Stats Section - Redesigned */}
      <motion.section
        initial={{ opacity: 0 }}
        whileInView={{ opacity: 1 }}
        viewport={{ once: true }}
        className="relative"
      >
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
          {[
            { number: "50K+", label: "Happy Customers", icon: Star },
            { number: "100+", label: "Digital Products", icon: Gift },
            { number: "24/7", label: "Instant Delivery", icon: Zap },
            { number: "30+", label: "Countries Served", icon: Trophy },
          ].map((stat, index) => (
            <motion.div
              key={index}
              initial={{ scale: 0.8, opacity: 0 }}
              whileInView={{ scale: 1, opacity: 1 }}
              viewport={{ once: true }}
              transition={{ delay: index * 0.1 }}
              className="bg-slate-900/50 backdrop-blur-sm border border-slate-800 rounded-2xl p-6 text-center hover:border-[#49baee]/30 transition-all duration-300 group"
            >
              <stat.icon className="w-8 h-8 mx-auto mb-4 text-[#49baee] group-hover:scale-110 transition-transform" />
              <h3 className="text-3xl font-black text-[#49baee] mb-2">
                {stat.number}
              </h3>
              <p className="text-slate-500 text-sm">{stat.label}</p>
            </motion.div>
          ))}
        </div>
      </motion.section>

      {/* Categories Section - Redesigned */}
      <section>
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-center mb-12"
        >
          <h2 className="text-4xl md:text-5xl font-black mb-4">
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#49baee] to-[#7dd3fc]">
              Shop by Category
            </span>
          </h2>
          <p className="text-slate-400 text-lg">Choose from our wide selection of digital products</p>
        </motion.div>

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
                  className="category-card group"
                  whileHover={{ scale: 1.02 }}
                  whileTap={{ scale: 0.98 }}
                >
                  <div className={`w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br ${category.color} p-4 shadow-lg group-hover:shadow-[0_0_30px_rgba(73,186,238,0.3)] transition-all duration-300`}>
                    <category.icon className="w-full h-full text-slate-950" />
                  </div>
                  <h3 className="font-bold text-lg mb-2 text-white">{category.name}</h3>
                  <p className="text-slate-500 text-sm">{category.description}</p>
                </motion.div>
              </Link>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Featured Products - Redesigned */}
      <section>
        <div className="flex items-center justify-between mb-12">
          <motion.h2
            initial={{ opacity: 0, x: -20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            className="text-4xl md:text-5xl font-black"
          >
            <span className="text-[#49baee]">🔥</span> Hot Deals
          </motion.h2>
          <motion.div
            initial={{ opacity: 0, x: 20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
          >
            <Link
              to="/shop"
              className="text-[#49baee] hover:text-[#5cc5f5] transition-colors flex items-center gap-2 group font-semibold"
            >
              View All Products
              <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
            </Link>
          </motion.div>
        </div>

        {loading ? (
          <div className="flex justify-center py-12">
            <LoadingSpinner size="lg" />
          </div>
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

      {/* CTA Section - New Design */}
      <motion.section
        initial={{ opacity: 0, y: 20 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true }}
        className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900/95 to-slate-900 border border-slate-800 p-12"
      >
        <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(73,186,238,0.1),transparent_70%)]" />
        
        <div className="relative z-10 text-center max-w-3xl mx-auto">
          <motion.div
            initial={{ scale: 0.8, opacity: 0 }}
            whileInView={{ scale: 1, opacity: 1 }}
            viewport={{ once: true }}
            className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#49baee]/20 mb-6"
          >
            <Sparkles className="w-8 h-8 text-[#49baee]" />
          </motion.div>
          
          <h2 className="text-4xl font-black mb-4">
            Ready to <span className="text-[#49baee]">Level Up</span>?
          </h2>
          <p className="text-slate-400 text-lg mb-8 leading-relaxed">
            Join thousands of gamers who trust Reload X for their digital needs. 
            Instant delivery, secure payments, and 24/7 customer support.
          </p>
          
          <Link
            to="/shop"
            className="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-[#49baee] to-[#5cc5f5] text-slate-950 font-bold rounded-xl hover:shadow-[0_0_30px_rgba(73,186,238,0.5)] hover:scale-105 transition-all duration-300"
          >
            Get Started Now
            <Sparkles className="w-5 h-5" />
          </Link>
        </div>
      </motion.section>
    </div>
  );
};

export default HomePage;