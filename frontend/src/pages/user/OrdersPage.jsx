import { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { Package, Eye, EyeOff, Copy, Check, Star } from "lucide-react";
import { useTranslation } from "react-i18next";
import { useNavigate } from "react-router-dom";
import { orderAPI } from "../../services/api";
import LoadingSpinner from "../../components/common/LoadingSpinner";
import toast from "react-hot-toast";

const OrdersPage = () => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [expandedOrder, setExpandedOrder] = useState(null);
  const [revealedCodes, setRevealedCodes] = useState(new Set());
  const [copiedCodes, setCopiedCodes] = useState(new Set());

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

  const toggleCodeVisibility = (itemId) => {
    if (revealedCodes.has(itemId)) {
      setRevealedCodes((prev) => {
        const newSet = new Set(prev);
        newSet.delete(itemId);
        return newSet;
      });
    } else {
      setRevealedCodes((prev) => new Set([...prev, itemId]));
      toast.success("Gift card code revealed!");
    }
  };

  const copyCode = async (code, itemId) => {
    try {
      await navigator.clipboard.writeText(code);
      setCopiedCodes(new Set([...copiedCodes, itemId]));
      toast.success("Code copied to clipboard!");

      setTimeout(() => {
        setCopiedCodes((prev) => {
          const newSet = new Set(prev);
          newSet.delete(itemId);
          return newSet;
        });
      }, 2000);
    } catch (error) {
      toast.error("Failed to copy code");
    }
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
        <p className="text-gray-400">Start shopping to see your orders here</p>
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
              <div>
                <h3 className="font-semibold">
                  {t("orders.orderNumber")}
                  {order.id.slice(0, 8)}
                </h3>
                <p className="text-sm text-gray-400">
                  {new Date(order.created_at).toLocaleDateString()} •
                  <span className={`ml-2 ${getStatusColor(order.status)}`}>
                    {order.status}
                  </span>
                </p>
              </div>
              <div className="text-right">
                <p className="text-lg font-bold">${order.total_amount}</p>
                <p className="text-sm text-gray-400">
                  {order.order_items.length} items
                </p>
              </div>
            </div>

            {/* Order Details */}
            {expandedOrder === order.id && (
              <motion.div
                initial={{ opacity: 0, height: 0 }}
                animate={{ opacity: 1, height: "auto" }}
                exit={{ opacity: 0, height: 0 }}
                className="mt-6 space-y-4 border-t border-dark-border pt-4"
              >
                {order.order_items.map((item) => (
                  <div key={item.id} className="flex items-start space-x-4">
                    <img
                      src={item.product?.image_url || "/images/placeholder.jpg"}
                      alt={item.product?.title}
                      className="w-20 h-20 object-cover rounded-lg"
                    />

                    <div className="flex-1">
                      <h4 className="font-medium">{item.product?.title}</h4>
                      <p className="text-sm text-gray-400">
                        Quantity: {item.quantity} • ${item.price} each
                      </p>

                      {/* Review Button for Completed Orders */}
                      {order.status === "completed" && !item.has_reviewed && (
                        <button
                          onClick={(e) => {
                            e.stopPropagation();
                            navigate(`/product/${item.product.id}?review=true`);
                          }}
                          className="mt-2 text-sm text-neon-purple hover:text-neon-pink transition-colors"
                        >
                          Write a Review
                        </button>
                      )}

                      {/* Gift Card Code Section for Completed Orders */}
                      {order.status === "completed" && item.decrypted_code && (
                        <div className="mt-3 p-3 bg-dark-bg rounded-lg">
                          <div className="flex items-center justify-between mb-2">
                            <span className="text-sm font-semibold text-gray-300">
                              Gift Card Code:
                            </span>
                            <button
                              onClick={(e) => {
                                e.stopPropagation();
                                toggleCodeVisibility(item.id);
                              }}
                              className="text-neon-purple hover:text-neon-pink transition-colors"
                              title={
                                revealedCodes.has(item.id)
                                  ? "Hide code"
                                  : "Show code"
                              }
                            >
                              {revealedCodes.has(item.id) ? (
                                <EyeOff className="w-4 h-4" />
                              ) : (
                                <Eye className="w-4 h-4" />
                              )}
                            </button>
                          </div>

                          {revealedCodes.has(item.id) ? (
                            <div className="flex items-center space-x-2">
                              <code className="bg-gray-900 px-3 py-2 rounded text-sm font-mono text-green-400 select-all">
                                {item.decrypted_code}
                              </code>
                              <button
                                onClick={(e) => {
                                  e.stopPropagation();
                                  copyCode(item.decrypted_code, item.id);
                                }}
                                className="text-neon-purple hover:text-neon-pink transition-colors"
                                title="Copy code"
                              >
                                {copiedCodes.has(item.id) ? (
                                  <Check className="w-4 h-4" />
                                ) : (
                                  <Copy className="w-4 h-4" />
                                )}
                              </button>
                            </div>
                          ) : (
                            <div className="bg-gray-900 px-3 py-2 rounded">
                              <span className="text-sm font-mono text-gray-500">
                                ••••••••••••••••
                              </span>
                            </div>
                          )}

                          {item.product_code?.expires_at && (
                            <p className="text-xs text-gray-500 mt-2">
                              Expires:{" "}
                              {new Date(
                                item.product_code.expires_at
                              ).toLocaleDateString()}
                            </p>
                          )}
                        </div>
                      )}

                      {/* Pending Order Message */}
                      {order.status === "processing" && (
                        <div className="mt-2 text-sm text-yellow-500">
                          Your gift card code will be available once the order
                          is completed.
                        </div>
                      )}
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
