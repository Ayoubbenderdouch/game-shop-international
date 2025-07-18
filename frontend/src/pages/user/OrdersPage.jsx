import { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { Package, ChevronDown, ChevronUp, ExternalLink } from "lucide-react";
import { useTranslation } from "react-i18next";
import { useNavigate } from "react-router-dom";
import { orderAPI } from "../../services/api";
import LoadingSpinner from "../../components/common/LoadingSpinner";
import GiftCardReveal from "../../components/cart/GiftCardReveal";
import toast from "react-hot-toast";

const OrdersPage = () => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [expandedOrder, setExpandedOrder] = useState(null);

  useEffect(() => {
    fetchOrders();
  }, []);

  const fetchOrders = async () => {
    try {
      const { data } = await orderAPI.getUserOrders();
      setOrders(data.orders);
    } catch (error) {
      console.error("Error fetching orders:", error);
      toast.error("Failed to load orders");
    } finally {
      setLoading(false);
    }
  };

  const toggleOrderExpand = (orderId) => {
    setExpandedOrder(expandedOrder === orderId ? null : orderId);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case "completed":
        return "text-green-500";
      case "processing":
        return "text-yellow-500";
      case "failed":
        return "text-red-500";
      case "refunded":
        return "text-gray-500";
      default:
        return "text-gray-400";
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center py-12">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  if (orders.length === 0) {
    return (
      <div className="text-center py-16">
        <Package className="w-24 h-24 mx-auto text-gray-600 mb-4" />
        <h2 className="text-2xl font-bold mb-2">{t("orders.empty")}</h2>
        <p className="text-gray-400 mb-6">
          Start shopping to see your orders here
        </p>
        <button onClick={() => navigate("/shop")} className="neon-button">
          Browse Products
        </button>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto">
      <h1 className="text-3xl font-bold mb-8 glow-text">{t("orders.title")}</h1>

      <div className="space-y-6">
        {orders.map((order, index) => (
          <motion.div
            key={order.id}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: index * 0.1 }}
            className="neon-card"
          >
            {/* Order Header */}
            <div
              className="flex items-center justify-between cursor-pointer"
              onClick={() => toggleOrderExpand(order.id)}
            >
              <div className="flex-1">
                <div className="flex flex-wrap items-center gap-4 mb-2">
                  <span className="text-sm text-gray-400">
                    {t("orders.orderNumber")}
                    {order.id.slice(0, 8)}
                  </span>
                  <span
                    className={`text-sm font-medium ${getStatusColor(
                      order.status
                    )}`}
                  >
                    {order.status.toUpperCase()}
                  </span>
                  <span className="text-sm text-gray-400">
                    {new Date(order.created_at).toLocaleDateString()}
                  </span>
                </div>
                <p className="text-lg font-semibold">${order.total_amount}</p>
              </div>

              <motion.div
                animate={{ rotate: expandedOrder === order.id ? 180 : 0 }}
                transition={{ duration: 0.3 }}
              >
                {expandedOrder === order.id ? (
                  <ChevronUp className="w-5 h-5 text-gray-400" />
                ) : (
                  <ChevronDown className="w-5 h-5 text-gray-400" />
                )}
              </motion.div>
            </div>

            {/* Order Details */}
            {expandedOrder === order.id && (
              <motion.div
                initial={{ opacity: 0, height: 0 }}
                animate={{ opacity: 1, height: "auto" }}
                className="mt-6 pt-6 border-t border-dark-border"
              >
                {order.order_items.map((item) => (
                  <div key={item.id} className="mb-6 last:mb-0">
                    <div className="flex items-start space-x-4">
                      <img
                        src={item.product.image_url || "/placeholder.png"}
                        alt={item.product.title}
                        className="w-20 h-20 object-cover rounded-lg"
                      />

                      <div className="flex-1">
                        <h4 className="font-medium mb-1">
                          {item.product.title}
                        </h4>
                        <p className="text-sm text-gray-400">
                          Quantity: {item.quantity} • ${item.price} each
                        </p>

                        {/* Review Button for Completed Orders */}
                        {order.status === "completed" && !item.has_reviewed && (
                          <button
                            onClick={(e) => {
                              e.stopPropagation();
                              navigate(
                                `/product/${item.product.id}?review=true`
                              );
                            }}
                            className="mt-2 text-sm text-neon-purple hover:text-neon-pink transition-colors flex items-center space-x-1"
                          >
                            <span>Write a Review</span>
                            <ExternalLink className="w-3 h-3" />
                          </button>
                        )}

                        {/* Gift Card Section for Completed Orders */}
                        {order.status === "completed" &&
                          item.decrypted_code && (
                            <GiftCardReveal item={item} order={order} />
                          )}

                        {/* Pending Order Message */}
                        {order.status === "processing" && (
                          <div className="mt-3 p-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                            <p className="text-sm text-yellow-500">
                              Your gift card code will be available once the
                              order is completed.
                            </p>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                ))}

                {/* Order Actions */}
                {order.status === "completed" && (
                  <div className="pt-4 border-t border-dark-border flex justify-between items-center">
                    <button
                      onClick={() => {
                        orderAPI.resendCodes(order.id);
                        toast.success("Gift card codes sent to your email!");
                      }}
                      className="text-sm text-neon-purple hover:text-neon-pink transition-colors"
                    >
                      Resend codes to email
                    </button>

                    <p className="text-xs text-gray-500">
                      Codes are encrypted for your security
                    </p>
                  </div>
                )}
              </motion.div>
            )}
          </motion.div>
        ))}
      </div>
    </div>
  );
};

export default OrdersPage;
