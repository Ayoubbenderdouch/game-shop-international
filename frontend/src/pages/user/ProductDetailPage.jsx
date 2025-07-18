import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import {
  ShoppingCart,
  Star,
  Globe,
  Package,
  Shield,
  ChevronLeft,
  User,
  Calendar,
  ThumbsUp,
} from "lucide-react";
import { useTranslation } from "react-i18next";
import toast from "react-hot-toast";
import { productAPI, reviewAPI } from "../../services/api";
import useStore from "../../store/useStore";
import { useAuth } from "../../hooks/useAuth";
import LoadingSpinner from "../../components/common/LoadingSpinner";

const ProductDetailPage = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { t } = useTranslation();
  const { addToCart } = useStore();
  const { user } = useAuth();

  const [product, setProduct] = useState(null);
  const [reviews, setReviews] = useState([]);
  const [loading, setLoading] = useState(true);
  const [quantity, setQuantity] = useState(1);
  const [showReviewForm, setShowReviewForm] = useState(false);
  const [reviewForm, setReviewForm] = useState({ rating: 5, comment: "" });
  const [userReview, setUserReview] = useState(null);
  const [canReview, setCanReview] = useState(false);

  useEffect(() => {
    fetchProduct();
    fetchReviews();
    if (user) {
      checkUserReview();
    }
  }, [id, user]);

  const fetchProduct = async () => {
    try {
      const { data } = await productAPI.getById(id);
      setProduct(data);
    } catch (error) {
      console.error("Error fetching product:", error);
      navigate("/shop");
    } finally {
      setLoading(false);
    }
  };

  const fetchReviews = async () => {
    try {
      const { data } = await reviewAPI.getProductReviews(id);
      setReviews(data.reviews);
    } catch (error) {
      console.error("Error fetching reviews:", error);
    }
  };

  const checkUserReview = async () => {
    if (!user) return;

    try {
      const { data } = await reviewAPI.getUserReviews();
      const existingReview = data.reviews.find((r) => r.product_id === id);
      if (existingReview) {
        setUserReview(existingReview);
        setCanReview(false);
      } else {
        // Check if user has purchased the product
        const userHasPurchased = true; // This would be checked via orders API
        setCanReview(userHasPurchased);
      }
    } catch (error) {
      console.error("Error checking user review:", error);
    }
  };

  const handleAddToCart = () => {
    if (product.stock_count >= quantity) {
      addToCart(product, quantity);
      toast.success("Added to cart!");
    }
  };

  const handleBuyNow = () => {
    handleAddToCart();
    navigate("/cart");
  };

  const handleReviewSubmit = async (e) => {
    e.preventDefault();
    if (!user) {
      toast.error("Please login to write a review");
      navigate("/login");
      return;
    }

    try {
      await reviewAPI.create({
        productId: id,
        rating: reviewForm.rating,
        comment: reviewForm.comment,
      });
      toast.success("Review submitted successfully!");
      setShowReviewForm(false);
      setReviewForm({ rating: 5, comment: "" });
      fetchReviews();
      fetchProduct(); // Refresh product to update average rating
      checkUserReview();
    } catch (error) {
      if (error.response?.status === 403) {
        toast.error("You must purchase this product to review it");
      } else {
        toast.error("Failed to submit review");
      }
    }
  };

  const renderStars = (rating, interactive = false, onChange = null) => {
    return (
      <div className="flex space-x-1">
        {[1, 2, 3, 4, 5].map((star) => (
          <button
            key={star}
            type={interactive ? "button" : undefined}
            onClick={interactive ? () => onChange?.(star) : undefined}
            className={interactive ? "cursor-pointer" : "cursor-default"}
            disabled={!interactive}
          >
            <Star
              className={`w-5 h-5 ${
                star <= rating
                  ? "fill-yellow-500 text-yellow-500"
                  : "text-gray-600"
              }`}
            />
          </button>
        ))}
      </div>
    );
  };

  if (loading) {
    return (
      <div className="flex justify-center py-12">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  if (!product) return null;

  return (
    <div className="max-w-6xl mx-auto">
      {/* Back Button */}
      <button
        onClick={() => navigate(-1)}
        className="mb-6 flex items-center space-x-2 text-gray-400 hover:text-white transition-colors"
      >
        <ChevronLeft className="w-5 h-5" />
        <span>Back</span>
      </button>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Product Image */}
        <motion.div
          initial={{ opacity: 0, x: -20 }}
          animate={{ opacity: 1, x: 0 }}
          className="neon-card"
        >
          <img
            src={product.image_url || "/images/placeholder.jpg"}
            alt={product.title}
            className="w-full h-96 object-cover rounded-lg"
          />

          {/* Tags */}
          <div className="flex flex-wrap gap-2 mt-4">
            {product.tags?.map((tag, index) => (
              <span
                key={index}
                className="px-3 py-1 bg-dark-bg rounded-full text-sm text-gray-400"
              >
                #{tag}
              </span>
            ))}
          </div>
        </motion.div>

        {/* Product Info */}
        <motion.div
          initial={{ opacity: 0, x: 20 }}
          animate={{ opacity: 1, x: 0 }}
          className="space-y-6"
        >
          <div>
            <h1 className="text-3xl font-bold mb-2">{product.title}</h1>
            <div className="flex items-center space-x-4 text-sm text-gray-400">
              <span className="flex items-center">
                <Package className="w-4 h-4 mr-1" />
                {product.category?.name}
              </span>
              <div className="flex items-center space-x-2">
                {renderStars(product.average_rating || 0)}
                <span>
                  {product.average_rating?.toFixed(1) || 0} (
                  {product.review_count || 0} reviews)
                </span>
              </div>
            </div>
          </div>

          {/* Price and Stock */}
          <div className="neon-card">
            <div className="flex items-center justify-between mb-4">
              <span className="text-4xl font-bold glow-text">
                ${product.price}
              </span>
              <span
                className={`text-sm ${
                  product.stock_count === 0
                    ? "text-red-500"
                    : product.stock_count < 10
                    ? "text-yellow-500"
                    : "text-green-500"
                }`}
              >
                {product.stock_count === 0
                  ? t("common.outOfStock")
                  : product.stock_count < 10
                  ? `${t("common.lowStock")} (${product.stock_count} left)`
                  : t("common.inStock")}
              </span>
            </div>

            {/* Quantity Selector */}
            {product.stock_count > 0 && (
              <div className="flex items-center space-x-4 mb-6">
                <span className="text-sm font-medium">Quantity:</span>
                <div className="flex items-center space-x-2">
                  <button
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                    className="w-8 h-8 rounded bg-dark-bg hover:bg-dark-hover flex items-center justify-center"
                  >
                    -
                  </button>
                  <span className="w-12 text-center">{quantity}</span>
                  <button
                    onClick={() =>
                      setQuantity(Math.min(product.stock_count, quantity + 1))
                    }
                    className="w-8 h-8 rounded bg-dark-bg hover:bg-dark-hover flex items-center justify-center"
                  >
                    +
                  </button>
                </div>
              </div>
            )}

            {/* Action Buttons */}
            <div className="flex space-x-4">
              <motion.button
                onClick={handleAddToCart}
                disabled={product.stock_count === 0}
                className="flex-1 neon-button flex items-center justify-center space-x-2"
                whileHover={product.stock_count > 0 ? { scale: 1.05 } : {}}
                whileTap={product.stock_count > 0 ? { scale: 0.95 } : {}}
              >
                <ShoppingCart className="w-5 h-5" />
                <span>{t("common.addToCart")}</span>
              </motion.button>
              <motion.button
                onClick={handleBuyNow}
                disabled={product.stock_count === 0}
                className="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg font-semibold hover:shadow-glow transition-shadow disabled:opacity-50 disabled:cursor-not-allowed"
                whileHover={product.stock_count > 0 ? { scale: 1.05 } : {}}
                whileTap={product.stock_count > 0 ? { scale: 0.95 } : {}}
              >
                {t("common.buyNow")}
              </motion.button>
            </div>
          </div>

          {/* Description */}
          <div className="neon-card">
            <h3 className="font-semibold mb-3">{t("product.description")}</h3>
            <p className="text-gray-300 leading-relaxed">
              {product.description}
            </p>
          </div>

          {/* Availability */}
          <div className="neon-card">
            <h3 className="font-semibold mb-3 flex items-center">
              <Globe className="w-5 h-5 mr-2" />
              {t("product.availability")}
            </h3>
            <div className="flex flex-wrap gap-2">
              {product.country_availability?.map((country, index) => (
                <span
                  key={index}
                  className="px-3 py-1 bg-dark-bg rounded-full text-sm"
                >
                  {country}
                </span>
              ))}
            </div>
          </div>
        </motion.div>
      </div>

      {/* Reviews Section */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.2 }}
        className="mt-12"
      >
        <div className="flex items-center justify-between mb-6">
          <h2 className="text-2xl font-bold">
            {t("product.reviews")} ({reviews.length})
          </h2>
          {user && !userReview && canReview && (
            <button
              onClick={() => setShowReviewForm(!showReviewForm)}
              className="neon-button text-sm"
            >
              {t("product.writeReview")}
            </button>
          )}
        </div>

        {/* Review Form */}
        {showReviewForm && (
          <motion.form
            initial={{ opacity: 0, height: 0 }}
            animate={{ opacity: 1, height: "auto" }}
            exit={{ opacity: 0, height: 0 }}
            onSubmit={handleReviewSubmit}
            className="neon-card mb-6"
          >
            <h3 className="text-lg font-semibold mb-4">Write Your Review</h3>
            <div className="mb-4">
              <label className="block text-sm font-medium mb-2">Rating</label>
              {renderStars(reviewForm.rating, true, (rating) =>
                setReviewForm((prev) => ({ ...prev, rating }))
              )}
            </div>
            <div className="mb-4">
              <label className="block text-sm font-medium mb-2">
                Comment (Optional)
              </label>
              <textarea
                value={reviewForm.comment}
                onChange={(e) =>
                  setReviewForm((prev) => ({
                    ...prev,
                    comment: e.target.value,
                  }))
                }
                className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                rows="4"
                placeholder="Share your experience with this product..."
              />
            </div>
            <div className="flex space-x-4">
              <button type="submit" className="neon-button">
                Submit Review
              </button>
              <button
                type="button"
                onClick={() => setShowReviewForm(false)}
                className="px-6 py-2 border border-dark-border rounded-lg hover:bg-dark-hover transition-colors"
              >
                Cancel
              </button>
            </div>
          </motion.form>
        )}

        {/* Reviews List */}
        <div className="space-y-4">
          {reviews.length === 0 ? (
            <div className="neon-card text-center py-8">
              <p className="text-gray-400">
                No reviews yet. Be the first to review this product!
              </p>
            </div>
          ) : (
            reviews.map((review) => (
              <motion.div
                key={review.id}
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                className="neon-card"
              >
                <div className="flex items-start justify-between mb-2">
                  <div>
                    <div className="flex items-center space-x-3">
                      <div className="w-10 h-10 bg-dark-bg rounded-full flex items-center justify-center">
                        <User className="w-5 h-5 text-gray-400" />
                      </div>
                      <div>
                        <p className="font-medium">
                          {review.user?.email?.split("@")[0] || "Anonymous"}
                        </p>
                        <div className="flex items-center space-x-2 text-sm text-gray-400">
                          {renderStars(review.rating)}
                          <span>•</span>
                          <span>
                            {new Date(review.created_at).toLocaleDateString()}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                {review.comment && (
                  <p className="text-gray-300 mt-3">{review.comment}</p>
                )}
              </motion.div>
            ))
          )}
        </div>
      </motion.div>
    </div>
  );
};

export default ProductDetailPage;
