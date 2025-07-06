import { motion } from 'framer-motion';
import { ShoppingCart, Zap, AlertCircle } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import useStore from '../../store/useStore';
import toast from 'react-hot-toast';

const ProductCard = ({ product }) => {
  const { t } = useTranslation();
  const { addToCart } = useStore();

  const handleAddToCart = (e) => {
    e.preventDefault();
    if (product.stock_count > 0) {
      addToCart(product);
      toast.success('Added to cart!');
    }
  };

  const stockStatus = () => {
    if (product.stock_count === 0) return { text: t('common.outOfStock'), color: 'text-red-500' };
    if (product.stock_count <= 10) return { text: t('common.lowStock'), color: 'text-yellow-500' };
    return { text: t('common.inStock'), color: 'text-green-500' };
  };

  const status = stockStatus();

  return (
    <motion.div
      whileHover={{ y: -5 }}
      transition={{ type: "spring", stiffness: 300 }}
    >
      <Link to={`/product/${product.id}`}>
        <div className="neon-card h-full flex flex-col">
          {/* Image */}
          <div className="relative overflow-hidden rounded-lg mb-4">
            <img
              src={product.image_url || '/images/placeholder.jpg'}
              alt={product.title}
              className="w-full h-48 object-cover transform transition-transform duration-300 hover:scale-110"
            />
            {product.is_preorder && (
              <div className="absolute top-2 right-2 bg-neon-pink text-white px-2 py-1 rounded-full text-xs font-bold">
                Pre-order
              </div>
            )}
            {product.stock_count <= 10 && product.stock_count > 0 && (
              <div className="absolute top-2 left-2 bg-yellow-500 text-black px-2 py-1 rounded-full text-xs font-bold flex items-center">
                <AlertCircle className="w-3 h-3 mr-1" />
                Low Stock
              </div>
            )}
          </div>

          {/* Content */}
          <div className="flex-1 flex flex-col">
            <h3 className="font-semibold text-lg mb-2 line-clamp-2">{product.title}</h3>
            
            {/* Tags */}
            <div className="flex flex-wrap gap-1 mb-3">
              {product.tags?.slice(0, 3).map((tag, index) => (
                <span
                  key={index}
                  className="text-xs bg-dark-bg px-2 py-1 rounded-full text-gray-400"
                >
                  #{tag}
                </span>
              ))}
            </div>

            {/* Price and Stock */}
            <div className="mt-auto">
              <div className="flex items-center justify-between mb-3">
                <span className="text-2xl font-bold glow-text">
                  ${product.price}
                </span>
                <span className={`text-sm ${status.color}`}>
                  {status.text}
                </span>
              </div>

              {/* Action Button */}
              <motion.button
                onClick={handleAddToCart}
                disabled={product.stock_count === 0 || product.is_preorder}
                className={`w-full py-2 px-4 rounded-lg font-semibold flex items-center justify-center space-x-2 transition-all
                  ${product.stock_count === 0 || product.is_preorder
                    ? 'bg-gray-700 text-gray-400 cursor-not-allowed'
                    : 'neon-button'
                  }`}
                whileHover={product.stock_count > 0 && !product.is_preorder ? { scale: 1.05 } : {}}
                whileTap={product.stock_count > 0 && !product.is_preorder ? { scale: 0.95 } : {}}
              >
                {product.stock_count === 0 ? (
                  <span>{t('common.outOfStock')}</span>
                ) : product.is_preorder ? (
                  <span>Coming Soon</span>
                ) : (
                  <>
                    <ShoppingCart className="w-4 h-4" />
                    <span>{t('common.addToCart')}</span>
                  </>
                )}
              </motion.button>
            </div>
          </div>
        </div>
      </Link>
    </motion.div>
  );
};

export default ProductCard;