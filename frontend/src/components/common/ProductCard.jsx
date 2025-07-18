import { motion } from "framer-motion";
import { ShoppingCart, Star, Package } from "lucide-react";
import { Link } from "react-router-dom";
import { useTranslation } from "react-i18next";
import useStore from "../../store/useStore";
import toast from "react-hot-toast";

const ProductCard = ({ product, index = 0 }) => {
  const { t } = useTranslation();
  const { addToCart } = useStore();

  const handleAddToCart = (e) => {
    e.preventDefault();
    addToCart(product);
    toast.success("Added to cart!");
  };

  const renderStars = (rating) => {
    return (
      <div className="flex items-center">
        {[1, 2, 3, 4, 5].map((star) => (
          <Star
            key={star}
            className={`w-4 h-4 ${
              star <= rating
                ? "fill-yellow-500 text-yellow-500"
                : "text-gray-600"
            }`}
          />
        ))}
      </div>
    );
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: index * 0.1 }}
      whileHover={{ y: -5 }}
      className="h-full"
    >
      <Link to={`/product/${product.id}`}>
        <div className="neon-card h-full flex flex-col group cursor-pointer">
          {/* Image Container */}
          <div className="relative overflow-hidden rounded-lg mb-4">
            <img
              src={product.image_url || "/images/placeholder.jpg"}
              alt={product.title}
              className="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-110"
            />
            {product.stock_count === 0 && (
              <div className="absolute inset-0 bg-black/70 flex items-center justify-center">
                <span className="text-red-500 font-bold">
                  {t("common.outOfStock")}
                </span>
              </div>
            )}
            {product.stock_count > 0 && product.stock_count < 10 && (
              <div className="absolute top-2 right-2 bg-yellow-500/80 text-black px-2 py-1 rounded text-xs font-semibold">
                {t("common.lowStock")}
              </div>
            )}
          </div>

          {/* Content */}
          <div className="flex-1 flex flex-col">
            {/* Category */}
            <div className="flex items-center text-xs text-gray-400 mb-1">
              <Package className="w-3 h-3 mr-1" />
              {product.category?.name || product.type}
            </div>

            {/* Title */}
            <h3 className="font-semibold mb-2 line-clamp-2">{product.title}</h3>

            {/* Rating */}
            <div className="flex items-center space-x-2 mb-2">
              {renderStars(product.average_rating || 0)}
              <span className="text-sm text-gray-400">
                {product.average_rating
                  ? product.average_rating.toFixed(1)
                  : "0.0"}
                ({product.review_count || 0})
              </span>
            </div>

            {/* Price and Action */}
            <div className="mt-auto flex items-center justify-between">
              <span className="text-2xl font-bold glow-text">
                ${product.price}
              </span>
              <motion.button
                onClick={handleAddToCart}
                disabled={product.stock_count === 0}
                className="p-2 rounded-lg bg-neon-purple/20 hover:bg-neon-purple/30 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                whileHover={product.stock_count > 0 ? { scale: 1.1 } : {}}
                whileTap={product.stock_count > 0 ? { scale: 0.9 } : {}}
              >
                <ShoppingCart className="w-5 h-5" />
              </motion.button>
            </div>
          </div>
        </div>
      </Link>
    </motion.div>
  );
};

export default ProductCard;
