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
  Zap,
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
        const userHasPurchased = true;
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
      fetchProduct();
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
                  ? "fill-[#49baee] text-[#49baee]"
                  : "text-slate-600"
              }`}
            />
          </button>
        ))}
      </div>
    );
  };

  if (loading) {
    return (
      <div className="flex justify-center py-20">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  if (!product) return null;

  return (
    <div className="max-w-7xl mx-auto">
      {/* Back Button */}
      <motion.button
        initial={{ opacity: 0, x: -20 }}
        animate={{ opacity: 1, x: 0 }}
        onClick={() => navigate(-1)}
        className="mb-8 flex items-center gap-2 text-slate-400 hover:text-[#49baee] transition-colors group"
      >
        <ChevronLeft className="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
        <span>Back to Shop</span>
      </motion.button>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
        {/* Product Image */}
        <motion.div
          initial={{ opacity: 0, x: -20 }}
          animate={{ opacity: 1, x: 0 }}
        >
          <div className="bg-slate-900/50 backdrop-blur-md border border-slate-800 rounded-2xl overflow-hidden">
            <img
              src={product.image_url || "/images/placeholder.jpg"}
              alt={product.title}
              className="w-full h-[500px] object-cover"
            />

            {/* Tags */}
            <div className="p-6 border-t border-slate-800">
              <div className="flex flex-wrap gap-2">
                {product.tags?.map((tag, index) => (
                  <span
                    key={index}
                    className="px-3 py-1 bg-slate-800/50 rounded-full text-sm text-slate-400"
                  >
                    #{tag}
                  </span>
                ))}
              </div>
            </div>
          </div>
        </motion.div>

        {/* Product Info */}
        <motion.div
          initial={{ opacity: 0, x: 20 }}
          animate={{ opacity: 1, x: 0 }}
          className="space-y-8"
        >
          {/* Title and Rating */}
          <div>
            <h1 className="text-4xl font-black mb-4">{product.title}</h1>
            <div className="flex items-center gap-6 text-sm">
              <span className="flex items-center gap-2 text-slate-400">
                <Package className="w-4 h-4" />
                {product.category?.name}
              </span>
              <div className="flex items-center gap-2">
                {renderStars(product.average_rating || 0)}
                <span className="text-slate-400">
                  {product.average_rating?.toFixed(1) || 0} (
                  {product.review_count || 0} reviews)
                </span>
              </div>
            </div>
          </div>

          {/* Price and Stock */}
          <div className="bg-slate-900/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6">
            <div className="flex items-center justify-between mb-6">
              <span className="text-5xl font-black text-[#49baee]">
                ${product.price}
              </span>
              <span
                className={`px-4 py-2 rounded-full text-sm font-semibold ${
                  product.stock_count === 0
                    ? "bg-red-500/20 text-red-400 border border-red-500/30"
                    : product.stock_count < 10
                    ? "bg-[#49baee]/20 text-[#49baee] border border-[#49baee]/30"
                    : "bg-emerald-500/20 text-emerald-400 border border-emerald-500/30"
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
              <div className="flex items-center gap-4 mb-6">
                <span className="text-sm font-medium text-slate-400">
                  Quantity:
                </span>
                <div className="flex items-center gap-2">
                  <button
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                    className="w-10 h-10 rounded-lg bg-slate-800 hover:bg-slate-700 flex items-center justify-center transition-colors"
                  >
                    -
                  </button>
                  <span className="w-16 text-center font-semibold">
                    {quantity}
                  </span>
                  <button
                    onClick={() =>
                      setQuantity(Math.min(product.stock_count, quantity + 1))
                    }
                    className="w-10 h-10 rounded-lg bg-slate-800 hover:bg-slate-700 flex items-center justify-center transition-colors"
                  >
                    +
                  </button>
                </div>
              </div>
            )}

            {/* Action Buttons */}
            <div className="flex gap-4">
              <motion.button
                onClick={handleAddToCart}
                disabled={product.stock_count === 0}
                className="flex-1 px-6 py-4 bg-[#49baee] text-slate-950 font-bold rounded-xl hover:bg-[#5cc5f5] hover:shadow-[0_0_25px_rgba(73,186,238,0.5)] transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                whileHover={product.stock_count > 0 ? { scale: 1.02 } : {}}
                whileTap={product.stock_count > 0 ? { scale: 0.98 } : {}}
              >
                <ShoppingCart className="w-5 h-5" />
                <span>{t("common.addToCart")}</span>
              </motion.button>
              <motion.button
                onClick={handleBuyNow}
                disabled={product.stock_count === 0}
                className="flex-1 px-6 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold rounded-xl hover:shadow-[0_0_25px_rgba(16,185,129,0.5)] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                whileHover={product.stock_count > 0 ? { scale: 1.02 } : {}}
                whileTap={product.stock_count > 0 ? { scale: 0.98 } : {}}
              >
                <Zap className="w-5 h-5" />
                <span>{t("common.buyNow")}</span>
              </motion.button>
            </div>
          </div>

          {/* Description */}
          <div className="bg-slate-900/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6">
            <h3 className="font-bold text-lg mb-4 text-[#49baee]">
              {t("product.description")}
            </h3>
            <p className="text-slate-300 leading-relaxed">
              {product.description}
            </p>
          </div>

          {/* Availability */}
          <div className="bg-slate-900/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6">
            <h3 className="font-bold text-lg mb-4 flex items-center gap-2 text-[#49baee]">
              <Globe className="w-5 h-5" />
              {t("product.availability")}
            </h3>
            <div className="flex flex-wrap gap-2">
              {product.country_availability?.map((country, index) => (
                <span
                  key={index}
                  className="px-4 py-2 bg-slate-800/50 rounded-lg text-sm"
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
        className="mt-16"
      >
        <div className="flex items-center justify-between mb-8">
          <h2 className="text-3xl font-bold">
            {t("product.reviews")} ({reviews.length})
          </h2>
          {user && !userReview && canReview && (
            <button
              onClick={() => setShowReviewForm(!showReviewForm)}
              className="px-6 py-3 bg-[#49baee] text-slate-950 font-bold rounded-xl hover:bg-[#5cc5f5] transition-colors"
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
            className="bg-slate-900/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 mb-8"
          >
            <h3 className="text-xl font-bold mb-6">Write Your Review</h3>
            <div className="mb-6">
              <label className="block text-sm font-medium mb-2 text-slate-300">
                Rating
              </label>
              {renderStars(reviewForm.rating, true, (rating) =>
                setReviewForm((prev) => ({ ...prev, rating }))
              )}
            </div>
            <div className="mb-6">
              <label className="block text-sm font-medium mb-2 text-slate-300">
                Your Review
              </label>
              <textarea
                value={reviewForm.comment}
                onChange={(e) =>
                  setReviewForm((prev) => ({
                    ...prev,
                    comment: e.target.value,
                  }))
                }
                className="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl focus:border-[#49baee] focus:outline-none focus:ring-2 focus:ring-[#49baee]/20 transition-all duration-300"
                rows="4"
                placeholder="Share your experience with this product..."
                required
              />
            </div>
            <div className="flex gap-4">
              <button
                type="submit"
                className="px-6 py-3 bg-[#49baee] text-slate-950 font-bold rounded-xl hover:bg-[#5cc5f5] transition-colors"
              >
                Submit Review
              </button>
              <button
                type="button"
                onClick={() => setShowReviewForm(false)}
                className="px-6 py-3 border-2 border-slate-700 rounded-xl hover:border-slate-600 transition-colors"
              >
                Cancel
              </button>
            </div>
          </motion.form>
        )}

        {/* Reviews List */}
        <div className="space-y-6">
          {reviews.length === 0 ? (
            <p className="text-center text-slate-400 py-12">
              No reviews yet. Be the first to review this product!
            </p>
          ) : (
            reviews.map((review) => (
              <motion.div
                key={review.id}
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                className="bg-slate-900/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6"
              >
                <div className="flex items-start justify-between mb-4">
                  <div>
                    <div className="flex items-center gap-3 mb-2">
                      <div className="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center">
                        <User className="w-5 h-5 text-slate-400" />
                      </div>
                      <div>
                        <p className="font-semibold">{review.user?.email}</p>
                        <div className="flex items-center gap-2 text-sm text-slate-400">
                          {renderStars(review.rating)}
                          <span>•</span>
                          <span>
                            {new Date(review.created_at).toLocaleDateString()}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                  {review.is_helpful_count > 0 && (
                    <span className="text-sm text-slate-400 flex items-center gap-1">
                      <ThumbsUp className="w-4 h-4" />
                      {review.is_helpful_count}
                    </span>
                  )}
                </div>
                <p className="text-slate-300">{review.comment}</p>
              </motion.div>
            ))
          )}
        </div>
      </motion.div>
    </div>
  );
};

export default ProductDetailPage;
