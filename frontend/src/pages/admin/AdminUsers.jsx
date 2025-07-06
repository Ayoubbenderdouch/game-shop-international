import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Search, User, Globe, Calendar, ShoppingBag, Star } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { adminAPI } from '../../services/api';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const AdminUsers = () => {
  const { t } = useTranslation();
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedUser, setSelectedUser] = useState(null);
  
  const [pagination, setPagination] = useState({
    page: 1,
    limit: 20,
    total: 0,
    totalPages: 0,
  });

  useEffect(() => {
    fetchUsers();
  }, [pagination.page, searchTerm]);

  const fetchUsers = async () => {
    try {
      setLoading(true);
      const { data } = await adminAPI.getUsers({
        page: pagination.page,
        limit: pagination.limit,
        search: searchTerm,
      });
      setUsers(data.users);
      setPagination(data.pagination);
    } catch (error) {
      console.error('Error fetching users:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = (e) => {
    e.preventDefault();
    setPagination(prev => ({ ...prev, page: 1 }));
    fetchUsers();
  };

  const getUserStats = (user) => {
    return {
      orders: user.stats?.total_orders || 0,
      reviews: user.stats?.total_reviews || 0,
      spent: user.stats?.total_spent || 0,
    };
  };

  if (loading && users.length === 0) {
    return (
      <div className="flex justify-center py-12">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-3xl font-bold mb-8 glow-text">Users Management</h1>

      {/* Search */}
      <form onSubmit={handleSearch} className="mb-8">
        <div className="relative max-w-md">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Search by email..."
            className="w-full pl-10 pr-4 py-3 bg-dark-card border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
          />
        </div>
      </form>

      {/* Users Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {users.map((user) => (
          <motion.div
            key={user.id}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="neon-card cursor-pointer"
            onClick={() => setSelectedUser(user)}
            whileHover={{ scale: 1.02 }}
          >
            <div className="flex items-start justify-between mb-4">
              <div className="flex items-center space-x-3">
                <div className="w-12 h-12 bg-gradient-to-br from-neon-purple to-neon-blue rounded-full flex items-center justify-center">
                  <User className="w-6 h-6 text-white" />
                </div>
                <div>
                  <p className="font-semibold">{user.email}</p>
                  <p className="text-sm text-gray-400">ID: {user.id.slice(0, 8)}</p>
                </div>
              </div>
              {user.is_admin && (
                <span className="px-3 py-1 bg-neon-purple/20 text-neon-purple rounded-full text-xs font-semibold">
                  Admin
                </span>
              )}
            </div>

            <div className="grid grid-cols-2 gap-4 mb-4">
              <div className="flex items-center space-x-2 text-sm">
                <Globe className="w-4 h-4 text-gray-400" />
                <span>{user.country || 'Unknown'}</span>
              </div>
              <div className="flex items-center space-x-2 text-sm">
                <Calendar className="w-4 h-4 text-gray-400" />
                <span>{new Date(user.created_at).toLocaleDateString()}</span>
              </div>
            </div>

            {/* User Stats */}
            <div className="grid grid-cols-3 gap-2">
              <div className="bg-dark-bg rounded-lg p-3 text-center">
                <ShoppingBag className="w-5 h-5 mx-auto mb-1 text-neon-blue" />
                <p className="text-lg font-semibold">{getUserStats(user).orders}</p>
                <p className="text-xs text-gray-400">Orders</p>
              </div>
              <div className="bg-dark-bg rounded-lg p-3 text-center">
                <Star className="w-5 h-5 mx-auto mb-1 text-yellow-500" />
                <p className="text-lg font-semibold">{getUserStats(user).reviews}</p>
                <p className="text-xs text-gray-400">Reviews</p>
              </div>
              <div className="bg-dark-bg rounded-lg p-3 text-center">
                <span className="text-xs text-green-500">$</span>
                <p className="text-lg font-semibold">{getUserStats(user).spent}</p>
                <p className="text-xs text-gray-400">Spent</p>
              </div>
            </div>
          </motion.div>
        ))}
      </div>

      {/* Pagination */}
      {pagination.totalPages > 1 && (
        <div className="flex justify-center mt-8 space-x-2">
          <button
            onClick={() => setPagination(prev => ({ ...prev, page: Math.max(1, prev.page - 1) }))}
            disabled={pagination.page === 1}
            className="px-4 py-2 border border-dark-border rounded-lg hover:bg-dark-hover disabled:opacity-50"
          >
            Previous
          </button>
          
          <span className="px-4 py-2">
            Page {pagination.page} of {pagination.totalPages}
          </span>
          
          <button
            onClick={() => setPagination(prev => ({ ...prev, page: Math.min(prev.totalPages, prev.page + 1) }))}
            disabled={pagination.page === pagination.totalPages}
            className="px-4 py-2 border border-dark-border rounded-lg hover:bg-dark-hover disabled:opacity-50"
          >
            Next
          </button>
        </div>
      )}

      {/* User Details Modal */}
      {selectedUser && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
          onClick={() => setSelectedUser(null)}
        >
          <motion.div
            initial={{ scale: 0.9, opacity: 0 }}
            animate={{ scale: 1, opacity: 1 }}
            exit={{ scale: 0.9, opacity: 0 }}
            className="bg-dark-card rounded-xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto"
            onClick={(e) => e.stopPropagation()}
          >
            <h2 className="text-2xl font-bold mb-6">User Details</h2>
            
            <div className="space-y-6">
              {/* User Info */}
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <p className="text-sm text-gray-400">Email</p>
                  <p className="font-medium">{selectedUser.email}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-400">User ID</p>
                  <p className="font-mono text-sm">{selectedUser.id}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-400">Country</p>
                  <p className="font-medium">{selectedUser.country || 'Unknown'}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-400">Role</p>
                  <p className="font-medium">{selectedUser.is_admin ? 'Admin' : 'Customer'}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-400">Joined</p>
                  <p className="font-medium">{new Date(selectedUser.created_at).toLocaleDateString()}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-400">Last Updated</p>
                  <p className="font-medium">{new Date(selectedUser.updated_at).toLocaleDateString()}</p>
                </div>
              </div>

              {/* Stats */}
              <div>
                <h3 className="font-semibold mb-3">Activity Stats</h3>
                <div className="grid grid-cols-3 gap-4">
                  <div className="bg-dark-bg rounded-lg p-4 text-center">
                    <ShoppingBag className="w-8 h-8 mx-auto mb-2 text-neon-blue" />
                    <p className="text-2xl font-bold">{getUserStats(selectedUser).orders}</p>
                    <p className="text-sm text-gray-400">Total Orders</p>
                  </div>
                  <div className="bg-dark-bg rounded-lg p-4 text-center">
                    <Star className="w-8 h-8 mx-auto mb-2 text-yellow-500" />
                    <p className="text-2xl font-bold">{getUserStats(selectedUser).reviews}</p>
                    <p className="text-sm text-gray-400">Reviews Written</p>
                  </div>
                  <div className="bg-dark-bg rounded-lg p-4 text-center">
                    <p className="text-2xl font-bold">
                      <span className="text-sm text-green-500">$</span>
                      {getUserStats(selectedUser).spent}
                    </p>
                    <p className="text-sm text-gray-400">Total Spent</p>
                  </div>
                </div>
              </div>
            </div>

            <div className="mt-6 flex justify-end">
              <button
                onClick={() => setSelectedUser(null)}
                className="px-6 py-2 border border-dark-border rounded-lg hover:bg-dark-hover transition-colors"
              >
                Close
              </button>
            </div>
          </motion.div>
        </motion.div>
      )}
    </div>
  );
};

export default AdminUsers;