import { motion } from 'framer-motion';
import { ShoppingCart, Trash2, ArrowRight } from 'lucide-react';
import { Link, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { loadStripe } from '@stripe/stripe-js';
import toast from 'react-hot-toast';
import useStore from '../../store/useStore';
import { useAuth } from '../../hooks/useAuth';
import { orderAPI } from '../../services/api';
import { STRIPE_PUBLIC_KEY } from '../../config/api';

const stripePromise = loadStripe(STRIPE_PUBLIC_KEY);

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

      const { data } = await orderAPI.createCheckout({ items });
      
      const stripe = await stripePromise;
      const { error } = await stripe.redirectToCheckout({
        sessionId: data.sessionId,
      });

      if (error) {
        throw error;
      }
    } catch (error) {
      console.error('Checkout error:', error);
      toast.error('Failed to create checkout session');
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
          <span>Continue Shopping</span>
          <ArrowRight className="w-4 h-4" />
        </Link>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto">
      <h1 className="text-3xl font-bold mb-8 glow-text">{t('cart.title')}</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Cart Items */}
        <div className="lg:col-span-2 space-y-4">
          {cart.map((item, index) => (
            <motion.div
              key={item.id}
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: index * 0.1 }}
              className="neon-card flex items-center space-x-4"
            >
              <img
                src={item.image_url || '/images/placeholder.jpg'}
                alt={item.title}
                className="w-24 h-24 object-cover rounded-lg"
              />
              
              <div className="flex-1">
                <h3 className="font-semibold mb-1">{item.title}</h3>
                <p className="text-sm text-gray-400">{item.category?.name}</p>
                <p className="text-lg font-bold text-neon-purple">${item.price}</p>
              </div>

              <div className="flex items-center space-x-2">
                <button
                  onClick={() => updateQuantity(item.id, item.quantity - 1)}
                  className="w-8 h-8 rounded bg-dark-bg hover:bg-dark-hover flex items-center justify-center"
                >
                  -
                </button>
                <span className="w-12 text-center">{item.quantity}</span>
                <button
                  onClick={() => updateQuantity(item.id, item.quantity + 1)}
                  disabled={item.quantity >= item.stock_count}
                  className="w-8 h-8 rounded bg-dark-bg hover:bg-dark-hover flex items-center justify-center disabled:opacity-50"
                >
                  +
                </button>
              </div>

              <button
                onClick={() => removeFromCart(item.id)}
                className="text-red-500 hover:text-red-400 transition-colors"
              >
                <Trash2 className="w-5 h-5" />
              </button>
            </motion.div>
          ))}
        </div>

        {/* Order Summary */}
        <motion.div
          initial={{ opacity: 0, x: 20 }}
          animate={{ opacity: 1, x: 0 }}
          className="neon-card h-fit sticky top-24"
        >
          <h2 className="text-xl font-semibold mb-4">Order Summary</h2>
          
          <div className="space-y-2 mb-4">
            {cart.map(item => (
              <div key={item.id} className="flex justify-between text-sm">
                <span className="text-gray-400">{item.title} x{item.quantity}</span>
                <span>${(item.price * item.quantity).toFixed(2)}</span>
              </div>
            ))}
          </div>
          
          <div className="border-t border-dark-border pt-4 mb-6">
            <div className="flex justify-between text-lg font-bold">
              <span>{t('cart.total')}</span>
              <span className="glow-text">${getCartTotal().toFixed(2)}</span>
            </div>
          </div>

          <motion.button
            onClick={handleCheckout}
            disabled={loading}
            className="w-full neon-button disabled:opacity-50"
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
          >
            {loading ? 'Processing...' : t('cart.checkout')}
          </motion.button>
          
          <button
            onClick={clearCart}
            className="w-full mt-3 text-sm text-gray-400 hover:text-red-500 transition-colors"
          >
            Clear Cart
          </button>
        </motion.div>
      </div>
    </div>
  );
};

export default CartPage;