import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Star, Trash2, Search, Filter, AlertCircle } from 'lucide-react';
import { adminAPI, reviewAPI } from '../../services/api';
import LoadingSpinner from '../../components/common/LoadingSpinner';
import toast from 'react-hot-toast';

const AdminReviews = () => {
  const [reviews, setReviews] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    search: '',
    rating: '',
    product: '',
    sortBy: 'created_at',
    sortOrder: 'desc'
  });
  const [products, setProducts] = useState([]);
  const [selectedReviews, setSelectedReviews] = useState(new Set());
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [reviewToDelete, setReviewToDelete] = useState(null);

  useEffect(() => {
    fetchReviews();
    fetchProducts();
  }, [filters]);

  const fetchReviews = async () => {
    try {
      setLoading(true);
      // In a real implementation, this would be an admin endpoint
      const { data } = await adminAPI.getReviews(filters);
      setReviews(data.reviews);
    } catch (error) {
      console.error('Error fetching reviews:', error);
      toast.error('Failed to fetch reviews');
    } finally {
      setLoading(false);
    }
  };

  const fetchProducts = async () => {
    try {
      const { data } = await adminAPI.getProducts();
      setProducts(data.products);
    } catch (error) {
      console.error('Error fetching products:', error);
    }
  };

  const handleDeleteReview = async (reviewId) => {
    try {
      await adminAPI.deleteReview(reviewId);
      toast.success('Review deleted successfully');
      fetchReviews();
      setShowDeleteModal(false);
      setReviewToDelete(null);
    } catch (error) {
      console.error('Error deleting review:', error);
      toast.error('Failed to delete review');
    }
  };

  const handleBulkDelete = async () => {
    if (selectedReviews.size === 0) {
      toast.error('No reviews selected');
      return;
    }

    if (!confirm(`Delete ${selectedReviews.size} selected reviews?`)) return;

    try {
      // Delete reviews one by one (in production, use a bulk endpoint)
      for (const reviewId of selectedReviews) {
        await adminAPI.deleteReview(reviewId);
      }
      toast.success(`${selectedReviews.size} reviews deleted`);
      setSelectedReviews(new Set());
      fetchReviews();
    } catch (error) {
      console.error('Error deleting reviews:', error);
      toast.error('Failed to delete some reviews');
    }
  };

  const toggleSelectReview = (reviewId) => {
    const newSelected = new Set(selectedReviews);
    if (newSelected.has(reviewId)) {
      newSelected.delete(reviewId);
    } else {
      newSelected.add(reviewId);
    }
    setSelectedReviews(newSelected);
  };

  const toggleSelectAll = () => {
    if (selectedReviews.size === reviews.length) {
      setSelectedReviews(new Set());
    } else {
      setSelectedReviews(new Set(reviews.map(r => r.id)));
    }
  };

  const renderStars = (rating) => {
    return (
      <div className="flex">
        {[1, 2, 3, 4, 5].map((star) => (
          <Star
            key={star}
            className={`w-4 h-4 ${
              star <= rating
                ? 'fill-yellow-500 text-yellow-500'
                : 'text-gray-600'
            }`}
          />
        ))}
      </div>
    );
  };

  const calculateStats = () => {
    if (reviews.length === 0) return { average: 0, distribution: {} };
    
    const total = reviews.reduce((sum, r) => sum + r.rating, 0);
    const average = total / reviews.length;
    
    const distribution = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };
    reviews.forEach(r => {
      distribution[r.rating]++;
    });
    
    return { average, distribution };
  };

  const stats = calculateStats();

  if (loading) {
    return (
      <div className="flex justify-center py-12">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-3xl font-bold glow-text">Review Management</h1>
        {selectedReviews.size > 0 && (
          <button
            onClick={handleBulkDelete}
            className="px-4 py-2 bg-red-500/20 text-red-500 rounded-lg hover:bg-red-500/30 transition-colors"
          >
            Delete Selected ({selectedReviews.size})
          </button>
        )}
      </div>

      {/* Stats Overview */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="neon-card"
        >
          <h3 className="text-lg font-semibold mb-4">Review Statistics</h3>
          <div className="space-y-3">
            <div className="flex items-center justify-between">
              <span>Average Rating</span>
              <div className="flex items-center space-x-2">
                {renderStars(Math.round(stats.average))}
                <span className="font-bold">{stats.average.toFixed(1)}</span>
              </div>
            </div>
            <div className="flex items-center justify-between">
              <span>Total Reviews</span>
              <span className="font-bold">{reviews.length}</span>
            </div>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.1 }}
          className="neon-card"
        >
          <h3 className="text-lg font-semibold mb-4">Rating Distribution</h3>
          <div className="space-y-2">
            {[5, 4, 3, 2, 1].map((rating) => (
              <div key={rating} className="flex items-center space-x-3">
                <span className="w-2">{rating}</span>
                <Star className="w-4 h-4 fill-yellow-500 text-yellow-500" />
                <div className="flex-1 bg-dark-bg rounded-full h-2 overflow-hidden">
                  <div
                    className="h-full bg-gradient-to-r from-neon-purple to-neon-pink"
                    style={{
                      width: `${reviews.length > 0 ? (stats.distribution[rating] / reviews.length * 100) : 0}%`
                    }}
                  />
                </div>
                <span className="text-sm text-gray-400 w-12 text-right">
                  {stats.distribution[rating]}
                </span>
              </div>
            ))}
          </div>
        </motion.div>
      </div>

      {/* Filters */}
      <div className="neon-card mb-6">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search reviews..."
              value={filters.search}
              onChange={(e) => setFilters({ ...filters, search: e.target.value })}
              className="w-full pl-10 pr-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
            />
          </div>
          
          <select
            value={filters.rating}
            onChange={(e) => setFilters({ ...filters, rating: e.target.value })}
            className="px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
          >
            <option value="">All Ratings</option>
            {[5, 4, 3, 2, 1].map(r => (
              <option key={r} value={r}>{r} Stars</option>
            ))}
          </select>

          <select
            value={filters.product}
            onChange={(e) => setFilters({ ...filters, product: e.target.value })}
            className="px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
          >
            <option value="">All Products</option>
            {products.map(p => (
              <option key={p.id} value={p.id}>{p.title}</option>
            ))}
          </select>

          <select
            value={`${filters.sortBy}-${filters.sortOrder}`}
            onChange={(e) => {
              const [sortBy, sortOrder] = e.target.value.split('-');
              setFilters({ ...filters, sortBy, sortOrder });
            }}
            className="px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
          >
            <option value="created_at-desc">Newest First</option>
            <option value="created_at-asc">Oldest First</option>
            <option value="rating-desc">Highest Rating</option>
            <option value="rating-asc">Lowest Rating</option>
          </select>
        </div>
      </div>

      {/* Reviews Table */}
      <div className="neon-card overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-dark-border">
                <th className="px-4 py-3 text-left">
                  <input
                    type="checkbox"
                    checked={selectedReviews.size === reviews.length && reviews.length > 0}
                    onChange={toggleSelectAll}
                    className="rounded bg-dark-bg border-dark-border"
                  />
                </th>
                <th className="px-4 py-3 text-left">Product</th>
                <th className="px-4 py-3 text-left">User</th>
                <th className="px-4 py-3 text-left">Rating</th>
                <th className="px-4 py-3 text-left">Comment</th>
                <th className="px-4 py-3 text-left">Date</th>
                <th className="px-4 py-3 text-left">Actions</th>
              </tr>
            </thead>
            <tbody>
              {reviews.map((review) => (
                <tr key={review.id} className="border-b border-dark-border hover:bg-dark-hover">
                  <td className="px-4 py-3">
                    <input
                      type="checkbox"
                      checked={selectedReviews.has(review.id)}
                      onChange={() => toggleSelectReview(review.id)}
                      className="rounded bg-dark-bg border-dark-border"
                    />
                  </td>
                  <td className="px-4 py-3">
                    <div className="flex items-center space-x-3">
                      <img
                        src={review.product?.image_url || '/images/placeholder.jpg'}
                        alt={review.product?.title}
                        className="w-10 h-10 object-cover rounded"
                      />
                      <span className="text-sm">{review.product?.title}</span>
                    </div>
                  </td>
                  <td className="px-4 py-3 text-sm">{review.user?.email}</td>
                  <td className="px-4 py-3">{renderStars(review.rating)}</td>
                  <td className="px-4 py-3">
                    <p className="text-sm text-gray-300 line-clamp-2 max-w-xs">
                      {review.comment || '-'}
                    </p>
                  </td>
                  <td className="px-4 py-3 text-sm text-gray-400">
                    {new Date(review.created_at).toLocaleDateString()}
                  </td>
                  <td className="px-4 py-3">
                    <button
                      onClick={() => {
                        setReviewToDelete(review);
                        setShowDeleteModal(true);
                      }}
                      className="text-red-500 hover:text-red-400 transition-colors"
                    >
                      <Trash2 className="w-5 h-5" />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Delete Confirmation Modal */}
      {showDeleteModal && reviewToDelete && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            className="bg-dark-card p-6 rounded-lg max-w-md w-full mx-4"
          >
            <div className="flex items-center space-x-3 mb-4">
              <AlertCircle className="w-6 h-6 text-red-500" />
              <h3 className="text-lg font-semibold">Delete Review</h3>
            </div>
            <p className="text-gray-400 mb-6">
              Are you sure you want to delete this review? This action cannot be undone.
            </p>
            <div className="space-y-2 mb-6 p-4 bg-dark-bg rounded-lg">
              <p className="text-sm">
                <span className="text-gray-400">Product:</span> {reviewToDelete.product?.title}
              </p>
              <p className="text-sm">
                <span className="text-gray-400">User:</span> {reviewToDelete.user?.email}
              </p>
              <p className="text-sm">
                <span className="text-gray-400">Rating:</span> {reviewToDelete.rating} stars
              </p>
            </div>
            <div className="flex space-x-4">
              <button
                onClick={() => handleDeleteReview(reviewToDelete.id)}
                className="flex-1 px-4 py-2 bg-red-500 rounded-lg hover:bg-red-600 transition-colors"
              >
                Delete
              </button>
              <button
                onClick={() => {
                  setShowDeleteModal(false);
                  setReviewToDelete(null);
                }}
                className="flex-1 px-4 py-2 bg-dark-bg rounded-lg hover:bg-dark-hover transition-colors"
              >
                Cancel
              </button>
            </div>
          </motion.div>
        </div>
      )}
    </div>
  );
};

export default AdminReviews;