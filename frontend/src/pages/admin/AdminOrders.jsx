import React,{ useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Search, Filter, Calendar, Globe, Package, ChevronDown, ChevronUp } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { adminAPI } from '../../services/api';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const AdminOrders = () => {
  const { t } = useTranslation();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [expandedOrders, setExpandedOrders] = useState(new Set());
  
  const [filters, setFilters] = useState({
    status: '',
    country: '',
    startDate: '',
    endDate: '',
    page: 1,
    limit: 20,
  });

  const [pagination, setPagination] = useState({
    page: 1,
    limit: 20,
    total: 0,
    totalPages: 0,
  });

  useEffect(() => {
    fetchOrders();
  }, [filters]);

  const fetchOrders = async () => {
    try {
      setLoading(true);
      const { data } = await adminAPI.getAllOrders(filters);
      setOrders(data.orders);
      setPagination(data.pagination);
    } catch (error) {
      console.error('Error fetching orders:', error);
    } finally {
      setLoading(false);
    }
  };

  const updateFilter = (key, value) => {
    setFilters(prev => ({ ...prev, [key]: value, page: 1 }));
  };

  const toggleOrderExpand = (orderId) => {
    const newExpanded = new Set(expandedOrders);
    if (newExpanded.has(orderId)) {
      newExpanded.delete(orderId);
    } else {
      newExpanded.add(orderId);
    }
    setExpandedOrders(newExpanded);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'completed': return 'bg-green-500/20 text-green-500';
      case 'processing': return 'bg-yellow-500/20 text-yellow-500';
      case 'pending': return 'bg-blue-500/20 text-blue-500';
      case 'failed': return 'bg-red-500/20 text-red-500';
      case 'refunded': return 'bg-gray-500/20 text-gray-500';
      default: return 'bg-gray-500/20 text-gray-500';
    }
  };

  const countries = [...new Set(orders.map(o => o.country).filter(Boolean))];

  if (loading && orders.length === 0) {
    return (
      <div className="flex justify-center py-12">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-3xl font-bold mb-8 glow-text">All Orders</h1>

      {/* Filters */}
      <div className="neon-card mb-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
          <div>
            <label className="block text-sm font-medium mb-2">Status</label>
            <select
              value={filters.status}
              onChange={(e) => updateFilter('status', e.target.value)}
              className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
            >
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="completed">Completed</option>
              <option value="failed">Failed</option>
              <option value="refunded">Refunded</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">Country</label>
            <select
              value={filters.country}
              onChange={(e) => updateFilter('country', e.target.value)}
              className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
            >
              <option value="">All Countries</option>
              {countries.map(country => (
                <option key={country} value={country}>{country}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">Start Date</label>
            <input
              type="date"
              value={filters.startDate}
              onChange={(e) => updateFilter('startDate', e.target.value)}
              className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
            />
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">End Date</label>
            <input
              type="date"
              value={filters.endDate}
              onChange={(e) => updateFilter('endDate', e.target.value)}
              className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
            />
          </div>

          <div className="flex items-end">
            <button
              onClick={() => setFilters({ status: '', country: '', startDate: '', endDate: '', page: 1, limit: 20 })}
              className="w-full px-4 py-2 border border-dark-border rounded-lg hover:bg-dark-hover transition-colors"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      {/* Orders Table */}
      <div className="neon-card overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-dark-border">
                <th className="text-left py-4 px-4">Order ID</th>
                <th className="text-left py-4 px-4">Customer</th>
                <th className="text-left py-4 px-4">Amount</th>
                <th className="text-left py-4 px-4">Items</th>
                <th className="text-left py-4 px-4">Country</th>
                <th className="text-left py-4 px-4">Status</th>
                <th className="text-left py-4 px-4">Date</th>
                <th className="text-center py-4 px-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              {orders.map((order) => (
                <React.Fragment key={order.id}>
                  <motion.tr
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    className="border-b border-dark-border hover:bg-dark-hover transition-colors cursor-pointer"
                    onClick={() => toggleOrderExpand(order.id)}
                  >
                    <td className="py-4 px-4 font-mono text-sm">{order.id.slice(0, 8)}</td>
                    <td className="py-4 px-4">
                      <div>
                        <p className="font-medium">{order.user?.email}</p>
                        <p className="text-xs text-gray-400">ID: {order.user_id?.slice(0, 8)}</p>
                      </div>
                    </td>
                    <td className="py-4 px-4 font-semibold">${order.total_amount}</td>
                    <td className="py-4 px-4">{order.order_items?.length || 0}</td>
                    <td className="py-4 px-4">
                      <div className="flex items-center space-x-1">
                        <Globe className="w-4 h-4 text-gray-400" />
                        <span>{order.country || 'N/A'}</span>
                      </div>
                    </td>
                    <td className="py-4 px-4">
                      <span className={`px-2 py-1 rounded-full text-xs ${getStatusColor(order.status)}`}>
                        {order.status}
                      </span>
                    </td>
                    <td className="py-4 px-4 text-sm">
                      {new Date(order.created_at).toLocaleDateString()}
                    </td>
                    <td className="py-4 px-4 text-center">
                      {expandedOrders.has(order.id) ? (
                        <ChevronUp className="w-5 h-5 text-gray-400 mx-auto" />
                      ) : (
                        <ChevronDown className="w-5 h-5 text-gray-400 mx-auto" />
                      )}
                    </td>
                  </motion.tr>

                  {/* Expanded Order Details */}
                  {expandedOrders.has(order.id) && (
                    <tr>
                      <td colSpan="8" className="bg-dark-bg/50">
                        <motion.div
                          initial={{ opacity: 0, height: 0 }}
                          animate={{ opacity: 1, height: 'auto' }}
                          exit={{ opacity: 0, height: 0 }}
                          className="p-6"
                        >
                          <h4 className="font-semibold mb-4">Order Items</h4>
                          <div className="space-y-2">
                            {order.order_items?.map((item) => (
                              <div key={item.id} className="flex items-center justify-between p-3 bg-dark-card rounded-lg">
                                <div className="flex items-center space-x-3">
                                  <Package className="w-8 h-8 text-neon-purple" />
                                  <div>
                                    <p className="font-medium">{item.product?.title || 'Product'}</p>
                                    <p className="text-sm text-gray-400">
                                      Quantity: {item.quantity} × ${item.price}
                                    </p>
                                  </div>
                                </div>
                                <span className="font-semibold">
                                  ${(item.quantity * item.price).toFixed(2)}
                                </span>
                              </div>
                            ))}
                          </div>

                          {/* Order Meta Info */}
                          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                            <div>
                              <p className="text-sm text-gray-400">Payment Intent</p>
                              <p className="font-mono text-xs">{order.stripe_payment_intent_id?.slice(0, 20) || 'N/A'}</p>
                            </div>
                            <div>
                              <p className="text-sm text-gray-400">Session ID</p>
                              <p className="font-mono text-xs">{order.stripe_session_id?.slice(0, 20) || 'N/A'}</p>
                            </div>
                            <div>
                              <p className="text-sm text-gray-400">Created</p>
                              <p className="text-sm">{new Date(order.created_at).toLocaleString()}</p>
                            </div>
                            <div>
                              <p className="text-sm text-gray-400">Updated</p>
                              <p className="text-sm">{new Date(order.updated_at).toLocaleString()}</p>
                            </div>
                          </div>
                        </motion.div>
                      </td>
                    </tr>
                  )}
                </React.Fragment>
              ))}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        {pagination.totalPages > 1 && (
          <div className="flex items-center justify-between p-4 border-t border-dark-border">
            <p className="text-sm text-gray-400">
              Showing {(pagination.page - 1) * pagination.limit + 1} to{' '}
              {Math.min(pagination.page * pagination.limit, pagination.total)} of {pagination.total} orders
            </p>
            
            <div className="flex space-x-2">
              <button
                onClick={() => updateFilter('page', Math.max(1, pagination.page - 1))}
                disabled={pagination.page === 1}
                className="px-4 py-2 border border-dark-border rounded-lg hover:bg-dark-hover disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>
              
              {[...Array(Math.min(5, pagination.totalPages))].map((_, i) => {
                const pageNumber = i + 1;
                return (
                  <button
                    key={pageNumber}
                    onClick={() => updateFilter('page', pageNumber)}
                    className={`px-4 py-2 rounded-lg ${
                      pagination.page === pageNumber
                        ? 'bg-neon-purple text-white'
                        : 'border border-dark-border hover:bg-dark-hover'
                    }`}
                  >
                    {pageNumber}
                  </button>
                );
              })}
              
              <button
                onClick={() => updateFilter('page', Math.min(pagination.totalPages, pagination.page + 1))}
                disabled={pagination.page === pagination.totalPages}
                className="px-4 py-2 border border-dark-border rounded-lg hover:bg-dark-hover disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default AdminOrders;