import { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Filter, Search, X, ChevronLeft, ChevronRight } from "lucide-react";
import { useSearchParams } from "react-router-dom";
import { useTranslation } from "react-i18next";
import { productAPI } from "../../services/api";
import ProductCard from "../../components/common/ProductCard";
import LoadingSpinner from "../../components/common/LoadingSpinner";

const ShopPage = () => {
  const { t } = useTranslation();
  const [searchParams, setSearchParams] = useSearchParams();
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [mobileFiltersOpen, setMobileFiltersOpen] = useState(false);

  const [filters, setFilters] = useState({
    category: searchParams.get("category") || "",
    minPrice: searchParams.get("minPrice") || "",
    maxPrice: searchParams.get("maxPrice") || "",
    search: searchParams.get("search") || "",
    sort: searchParams.get("sort") || "latest",
  });

  const [pagination, setPagination] = useState({
    page: 1,
    limit: 12,
    total: 0,
    totalPages: 0,
  });

  useEffect(() => {
    fetchCategories();
  }, []);

  useEffect(() => {
    fetchProducts();
  }, [filters, pagination.page]);

  const fetchCategories = async () => {
    try {
      const { data } = await productAPI.getCategories();
      setCategories(data);
    } catch (error) {
      console.error("Error fetching categories:", error);
    }
  };

  const fetchProducts = async () => {
    setLoading(true);
    try {
      const params = {
        page: pagination.page,
        limit: pagination.limit,
        ...filters,
      };

      const { data } = await productAPI.getAll(params);
      setProducts(data.products);
      setPagination(data.pagination);
    } catch (error) {
      console.error("Error fetching products:", error);
    } finally {
      setLoading(false);
    }
  };

  const updateFilter = (key, value) => {
    setFilters((prev) => ({ ...prev, [key]: value }));
    setPagination((prev) => ({ ...prev, page: 1 }));

    if (value) {
      searchParams.set(key, value);
    } else {
      searchParams.delete(key);
    }
    setSearchParams(searchParams);
  };

  const clearFilters = () => {
    setFilters({
      category: "",
      minPrice: "",
      maxPrice: "",
      search: "",
      sort: "latest",
    });
    setSearchParams({});
  };

  const FilterSection = () => (
    <div className="space-y-6">
      {/* Search */}
      <div>
        <label className="block text-sm font-medium mb-2 text-slate-300">
          {t("common.search")}
        </label>
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 w-4 h-4" />
          <input
            type="text"
            value={filters.search}
            onChange={(e) => updateFilter("search", e.target.value)}
            placeholder="Search products..."
            className="w-full pl-10 pr-4 py-3 bg-slate-900/50 border border-slate-800 rounded-xl focus:border-[#49baee] focus:outline-none focus:ring-2 focus:ring-[#49baee]/20 transition-all duration-300"
          />
        </div>
      </div>

      {/* Categories */}
      <div>
        <label className="block text-sm font-medium mb-2 text-slate-300">
          {t("shop.filters.category")}
        </label>
        <select
          value={filters.category}
          onChange={(e) => updateFilter("category", e.target.value)}
          className="w-full px-4 py-3 bg-slate-900/50 border border-slate-800 rounded-xl focus:border-[#49baee] focus:outline-none focus:ring-2 focus:ring-[#49baee]/20 transition-all duration-300"
        >
          <option value="">{t("shop.filters.all")}</option>
          {categories.map((cat) => (
            <option key={cat.id} value={cat.id}>
              {cat.name}
            </option>
          ))}
        </select>
      </div>

      {/* Price Range */}
      <div>
        <label className="block text-sm font-medium mb-2 text-slate-300">
          {t("shop.filters.priceRange")}
        </label>
        <div className="flex gap-2">
          <input
            type="number"
            value={filters.minPrice}
            onChange={(e) => updateFilter("minPrice", e.target.value)}
            placeholder="Min"
            className="w-1/2 px-3 py-3 bg-slate-900/50 border border-slate-800 rounded-xl focus:border-[#49baee] focus:outline-none focus:ring-2 focus:ring-[#49baee]/20 transition-all duration-300"
          />
          <input
            type="number"
            value={filters.maxPrice}
            onChange={(e) => updateFilter("maxPrice", e.target.value)}
            placeholder="Max"
            className="w-1/2 px-3 py-3 bg-slate-900/50 border border-slate-800 rounded-xl focus:border-[#49baee] focus:outline-none focus:ring-2 focus:ring-[#49baee]/20 transition-all duration-300"
          />
        </div>
      </div>

      {/* Sort */}
      <div>
        <label className="block text-sm font-medium mb-2 text-slate-300">
          {t("common.sort")}
        </label>
        <select
          value={filters.sort}
          onChange={(e) => updateFilter("sort", e.target.value)}
          className="w-full px-4 py-3 bg-slate-900/50 border border-slate-800 rounded-xl focus:border-[#49baee] focus:outline-none focus:ring-2 focus:ring-[#49baee]/20 transition-all duration-300"
        >
          <option value="latest">{t("shop.sort.latest")}</option>
          <option value="priceLow">{t("shop.sort.priceLow")}</option>
          <option value="priceHigh">{t("shop.sort.priceHigh")}</option>
          <option value="popular">{t("shop.sort.popular")}</option>
        </select>
      </div>

      {/* Clear Filters */}
      <button
        onClick={clearFilters}
        className="w-full py-3 border-2 border-[#49baee]/30 text-[#49baee] rounded-xl hover:bg-[#49baee] hover:text-slate-950 hover:border-[#49baee] font-semibold transition-all duration-300"
      >
        Clear Filters
      </button>
    </div>
  );

  return (
    <div>
      {/* Page Header */}
      <div className="mb-12 text-center">
        <motion.h1
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="text-5xl font-black mb-4"
        >
          <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#49baee] to-[#7dd3fc]">
            {t("shop.title")}
          </span>
        </motion.h1>
        <motion.p
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.1 }}
          className="text-slate-400 text-lg"
        >
          Browse our collection of digital products
        </motion.p>
      </div>

      <div className="flex gap-8">
        {/* Desktop Filters */}
        <motion.aside
          initial={{ opacity: 0, x: -20 }}
          animate={{ opacity: 1, x: 0 }}
          className="hidden lg:block w-80 flex-shrink-0"
        >
          <div className="bg-slate-900/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 sticky top-24">
            <h2 className="text-lg font-bold mb-6 flex items-center gap-2 text-[#49baee]">
              <Filter className="w-5 h-5" />
              Filters
            </h2>
            <FilterSection />
          </div>
        </motion.aside>

        {/* Main Content */}
        <div className="flex-1">
          {/* Mobile Filter Button */}
          <button
            onClick={() => setMobileFiltersOpen(true)}
            className="lg:hidden mb-6 px-6 py-3 bg-[#49baee] text-slate-950 font-bold rounded-xl hover:bg-[#5cc5f5] transition-all duration-300 flex items-center gap-2"
          >
            <Filter className="w-5 h-5" />
            <span>Filters</span>
          </button>

          {/* Products Grid */}
          {loading ? (
            <div className="flex justify-center py-20">
              <LoadingSpinner size="lg" />
            </div>
          ) : products.length === 0 ? (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center py-20"
            >
              <p className="text-slate-400 text-lg">No products found</p>
              <button
                onClick={clearFilters}
                className="mt-4 text-[#49baee] hover:text-[#5cc5f5] transition-colors"
              >
                Clear filters and try again
              </button>
            </motion.div>
          ) : (
            <>
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
              >
                {products.map((product, index) => (
                  <motion.div
                    key={product.id}
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: index * 0.05 }}
                  >
                    <ProductCard product={product} />
                  </motion.div>
                ))}
              </motion.div>

              {/* Pagination */}
              {pagination.totalPages > 1 && (
                <motion.div
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  className="flex justify-center items-center mt-12 gap-2"
                >
                  <button
                    onClick={() =>
                      setPagination((prev) => ({
                        ...prev,
                        page: Math.max(1, prev.page - 1),
                      }))
                    }
                    disabled={pagination.page === 1}
                    className="p-2 rounded-lg bg-slate-900/50 border border-slate-800 text-slate-400 hover:text-[#49baee] hover:border-[#49baee]/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
                  >
                    <ChevronLeft className="w-5 h-5" />
                  </button>

                  {[...Array(pagination.totalPages)].map((_, i) => (
                    <motion.button
                      key={i}
                      onClick={() =>
                        setPagination((prev) => ({ ...prev, page: i + 1 }))
                      }
                      whileHover={{ scale: 1.1 }}
                      whileTap={{ scale: 0.95 }}
                      className={`w-10 h-10 rounded-lg font-semibold transition-all duration-300 ${
                        pagination.page === i + 1
                          ? "bg-[#49baee] text-slate-950 shadow-[0_0_20px_rgba(73,186,238,0.4)]"
                          : "bg-slate-900/50 border border-slate-800 text-slate-400 hover:text-[#49baee] hover:border-[#49baee]/50"
                      }`}
                    >
                      {i + 1}
                    </motion.button>
                  ))}

                  <button
                    onClick={() =>
                      setPagination((prev) => ({
                        ...prev,
                        page: Math.min(pagination.totalPages, prev.page + 1),
                      }))
                    }
                    disabled={pagination.page === pagination.totalPages}
                    className="p-2 rounded-lg bg-slate-900/50 border border-slate-800 text-slate-400 hover:text-[#49baee] hover:border-[#49baee]/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
                  >
                    <ChevronRight className="w-5 h-5" />
                  </button>
                </motion.div>
              )}
            </>
          )}
        </div>
      </div>

      {/* Mobile Filters Modal */}
      <AnimatePresence>
        {mobileFiltersOpen && (
          <>
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
              onClick={() => setMobileFiltersOpen(false)}
            />
            <motion.div
              initial={{ x: "100%" }}
              animate={{ x: 0 }}
              exit={{ x: "100%" }}
              transition={{ type: "spring", damping: 20 }}
              className="fixed right-0 top-0 h-full w-80 bg-slate-900 border-l border-slate-800 z-50 lg:hidden overflow-y-auto"
            >
              <div className="p-6">
                <div className="flex items-center justify-between mb-6">
                  <h2 className="text-lg font-bold flex items-center gap-2 text-[#49baee]">
                    <Filter className="w-5 h-5" />
                    Filters
                  </h2>
                  <button
                    onClick={() => setMobileFiltersOpen(false)}
                    className="p-2 hover:bg-slate-800 rounded-lg transition-colors"
                  >
                    <X className="w-5 h-5" />
                  </button>
                </div>
                <FilterSection />
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </div>
  );
};

export default ShopPage;