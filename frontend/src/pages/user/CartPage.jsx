import { useState } from 'react'; // Add missing import
import { motion } from 'framer-motion';
import { ShoppingCart, Trash2, ArrowRight } from 'lucide-react';
import { Link, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import toast from 'react-hot-toast';
import useStore from '../../store/useStore';
import { useAuth } from '../../hooks/useAuth';
import { orderAPI } from '../../services/api';

const CartPage = () => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { cart, updateQuantity, removeFromCart, getCartTotal, clearCart } = useStore();
  const { user } = useAuth();
  const [loading, setLoading] = useState(false);

  const handleCheckout = async () => {
    if (!user) {
      toast.error('Please login to checkout');
      navigate('/login');
      return;
    }

    setLoading(true);
    try {
      const items = cart.map(item => ({
        productId: item.id,
        quantity: item.quantity,
      }));

      // Mock checkout process
      const { data } = await orderAPI.createCheckout({ items });
      
      // Simulate payment processing
      toast.loading('Processing payment...', { id: 'checkout' });
      
      // Mock payment delay
      await new Promise(resolve => setTimeout(resolve, 2000));
      
      toast.success('Payment successful!', { id: 'checkout' });
      
      // Clear cart and redirect to orders
      clearCart();
      navigate('/orders');
      
    } catch (error) {
      console.error('Checkout error:', error);
      toast.error('Failed to process checkout', { id: 'checkout' });
    } finally {
      setLoading(false);
    }
  };

  if (cart.length === 0) {
    return (
      <div className="text-center py-16">
        <ShoppingCart className="w-24 h-24 mx-auto text-gray-600 mb-4" />
        <h2 className="text-2xl font-bold mb-2">{t('cart.empty')}</h2>
        <p className="text-gray-400 mb-8">Start shopping to add items to your cart</p>
        <Link to="/shop" className="neon-button inline-flex items-center space-x-2">
          <span>Browse Products</span>
          <ArrowRight className="w-5 h-5" />
        </Link>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto">
      <h1 className="text-3xl font-bold mb-8">{t('cart.title')}</h1>
      
      <div className="space-y-4 mb-8">
        {cart.map((item) => (
          <motion.div
            key={item.id}
            layout
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            className="neon-card flex items-center space-x-4"
          >
            <img
              src={item.image_url}
              alt={item.title}
              className="w-24 h-24 object-cover rounded-lg"
            />
            
            <div className="flex-1">
              <h3 className="font-semibold">{item.title}</h3>
              <p className="text-neon-purple">${item.price}</p>
            </div>
            
            <div className="flex items-center space-x-2">
              <button
                onClick={() => updateQuantity(item.id, Math.max(1, item.quantity - 1))}
                className="w-8 h-8 rounded border border-dark-border hover:bg-dark-hover transition-colors"
              >
                -
              </button>
              <span className="w-12 text-center">{item.quantity}</span>
              <button
                onClick={() => updateQuantity(item.id, item.quantity + 1)}
                className="w-8 h-8 rounded border border-dark-border hover:bg-dark-hover transition-colors"
              >
                +
              </button>
            </div>
            
            <button
              onClick={() => removeFromCart(item.id)}
              className="p-2 hover:bg-dark-hover rounded transition-colors"
            >
              <Trash2 className="w-5 h-5 text-red-500" />
            </button>
          </motion.div>
        ))}
      </div>
      
      <div className="neon-card">
        <div className="flex justify-between items-center mb-4">
          <span className="text-xl font-semibold">{t('cart.total')}:</span>
          <span className="text-2xl font-bold text-neon-purple">
            ${getCartTotal().toFixed(2)}
          </span>
        </div>
        
        <button
          onClick={handleCheckout}
          disabled={loading}
          className="w-full neon-button py-3 font-medium disabled:opacity-50"
        >
          {loading ? 'Processing...' : t('cart.checkout')}
        </button>
      </div>
    </div>
  );
};

export default CartPage;