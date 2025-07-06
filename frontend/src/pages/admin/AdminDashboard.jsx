import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { DollarSign, Package, ShoppingBag, AlertTriangle, TrendingUp, Globe } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { adminAPI } from '../../services/api';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const AdminDashboard = () => {
  const { t } = useTranslation();
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardStats();
  }, []);

  const fetchDashboardStats = async () => {
    try {
      const { data } = await adminAPI.getDashboard();
      setStats(data);
    } catch (error) {
      console.error('Error fetching dashboard stats:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center py-12">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  const statCards = [
    {
      title: t('admin.dashboard.totalRevenue'),
      value: `$${stats?.totalSales?.toFixed(2) || '0.00'}`,
      icon: DollarSign,
      color: 'from-green-500 to-emerald-500',
    },
    {
      title: t('admin.dashboard.totalOrders'),
      value: stats?.totalOrders || 0,
      icon: ShoppingBag,
      color: 'from-blue-500 to-cyan-500',
    },
    {
      title: t('admin.dashboard.totalProducts'),
      value: stats?.totalProducts || 0,
      icon: Package,
      color: 'from-purple-500 to-pink-500',
    },
    {
      title: 'Total Stock',
      value: stats?.totalStock || 0,
      icon: Package,
      color: 'from-orange-500 to-red-500',
    },
  ];

  return (
    <div className="space-y-8">
      <h1 className="text-3xl font-bold glow-text">{t('admin.dashboard.title')}</h1>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {statCards.map((stat, index) => (
          <motion.div
            key={stat.title}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: index * 0.1 }}
            className="neon-card"
          >
            <div className="flex items-center justify-between mb-4">
              <div className={`p-3 rounded-lg bg-gradient-to-br ${stat.color}`}>
                <stat.icon className="w-6 h-6 text-white" />
              </div>
              <TrendingUp className="w-5 h-5 text-green-500" />
            </div>
            <h3 className="text-sm text-gray-400 mb-1">{stat.title}</h3>
            <p className="text-2xl font-bold">{stat.value}</p>
          </motion.div>
        ))}
      </div>

      {/* Low Stock Alert */}
      {stats?.lowStockProducts?.length > 0 && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="neon-card border-yellow-500/50"
        >
          <div className="flex items-center mb-4">
            <AlertTriangle className="w-5 h-5 text-yellow-500 mr-2" />
            <h2 className="text-xl font-semibold">{t('admin.dashboard.lowStock')}</h2>
          </div>
          <div className="space-y-2">
            {stats.lowStockProducts.map((product) => (
              <div key={product.id} className="flex justify-between items-center py-2 border-b border-dark-border last:border-0">
                <span>{product.title}</span>
                <span className="text-yellow-500 font-semibold">{product.stock_count} left</span>
              </div>
            ))}
          </div>
        </motion.div>
      )}

      {/* Orders by Country */}
      {stats?.ordersByCountry && Object.keys(stats.ordersByCountry).length > 0 && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="neon-card"
        >
          <div className="flex items-center mb-4">
            <Globe className="w-5 h-5 text-neon-blue mr-2" />
            <h2 className="text-xl font-semibold">Orders by Country</h2>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {Object.entries(stats.ordersByCountry).map(([country, count]) => (
              <div key={country} className="text-center">
                <p className="text-2xl font-bold text-neon-purple">{count}</p>
                <p className="text-sm text-gray-400">{country}</p>
              </div>
            ))}
          </div>
        </motion.div>
      )}

      {/* Recent Orders */}
      {stats?.recentOrders?.length > 0 && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="neon-card"
        >
          <h2 className="text-xl font-semibold mb-4">Recent Orders</h2>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-dark-border">
                  <th className="text-left py-2">Order ID</th>
                  <th className="text-left py-2">Customer</th>
                  <th className="text-left py-2">Amount</th>
                  <th className="text-left py-2">Status</th>
                  <th className="text-left py-2">Date</th>
                </tr>
              </thead>
              <tbody>
                {stats.recentOrders.map((order) => (
                  <tr key={order.id} className="border-b border-dark-border">
                    <td className="py-2">{order.id.slice(0, 8)}</td>
                    <td className="py-2">{order.user?.email}</td>
                    <td className="py-2">${order.total_amount}</td>
                    <td className="py-2">
                      <span className={`px-2 py-1 rounded-full text-xs ${
                        order.status === 'completed' ? 'bg-green-500/20 text-green-500' :
                        order.status === 'processing' ? 'bg-yellow-500/20 text-yellow-500' :
                        'bg-red-500/20 text-red-500'
                      }`}>
                        {order.status}
                      </span>
                    </td>
                    <td className="py-2">{new Date(order.created_at).toLocaleDateString()}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </motion.div>
      )}
    </div>
  );
};

export default AdminDashboard;