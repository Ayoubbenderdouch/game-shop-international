const { supabaseAdmin } = require("../config/supabase");
const { logAudit } = require("../utils/auditLogger");
const logger = require("../utils/logger");

const getDashboardStats = async (req, res) => {
  try {
    const { data: totalSalesData } = await supabaseAdmin
      .from("orders")
      .select("total_amount")
      .eq("status", "completed");

    const totalSales =
      totalSalesData?.reduce(
        (sum, order) => sum + parseFloat(order.total_amount),
        0
      ) || 0;

    const { count: totalOrders } = await supabaseAdmin
      .from("orders")
      .select("*", { count: "exact" })
      .eq("status", "completed");

    const { count: totalProducts } = await supabaseAdmin
      .from("products")
      .select("*", { count: "exact" })
      .eq("is_active", true);

    const { data: totalStockData } = await supabaseAdmin
      .from("products")
      .select("stock_count");

    const totalStock =
      totalStockData?.reduce((sum, product) => sum + product.stock_count, 0) ||
      0;

    const { data: soldCodesData } = await supabaseAdmin
      .from("product_codes")
      .select("id")
      .eq("is_used", true);

    const soldCards = soldCodesData?.length || 0;

    const { data: ordersByCountry } = await supabaseAdmin
      .from("orders")
      .select("country")
      .eq("status", "completed");

    const countryStats = {};
    ordersByCountry?.forEach((order) => {
      countryStats[order.country] = (countryStats[order.country] || 0) + 1;
    });

    const { data: recentOrders } = await supabaseAdmin
      .from("orders")
      .select(
        `
                id,
                total_amount,
                status,
                created_at,
                user:users(email)
            `
      )
      .order("created_at", { ascending: false })
      .limit(10);

    const { data: lowStockProducts } = await supabaseAdmin
      .from("products")
      .select("id, title, stock_count")
      .lte("stock_count", 10)
      .eq("is_active", true)
      .order("stock_count");

    res.json({
      totalSales,
      totalOrders,
      totalProducts,
      totalStock,
      soldCards,
      ordersByCountry: countryStats,
      recentOrders,
      lowStockProducts,
    });
  } catch (error) {
    logger.error("Get dashboard stats error:", error);
    res.status(500).json({ error: "Failed to fetch dashboard stats" });
  }
};

const getUsers = async (req, res) => {
  try {
    const { page = 1, limit = 20, search } = req.query;
    const offset = (page - 1) * limit;

    let query = supabaseAdmin.from("users").select("*", { count: "exact" });

    if (search) {
      query = query.ilike("email", `%${search}%`);
    }

    const {
      data: users,
      error,
      count,
    } = await query
      .range(offset, offset + limit - 1)
      .order("created_at", { ascending: false });

    if (error) {
      throw error;
    }

    // Fetch user stats including reviews
    const userIds = users.map(u => u.id);
    
    // Get review counts
    const { data: reviewCounts } = await supabaseAdmin
      .from("reviews")
      .select("user_id")
      .in("user_id", userIds)
      .eq("is_deleted", false);

    // Get order counts and total spent
    const { data: orderStats } = await supabaseAdmin
      .from("orders")
      .select("user_id, total_amount, status")
      .in("user_id", userIds)
      .eq("status", "completed");

    // Aggregate stats
    const userStats = {};
    userIds.forEach(userId => {
      userStats[userId] = {
        total_reviews: reviewCounts?.filter(r => r.user_id === userId).length || 0,
        total_orders: orderStats?.filter(o => o.user_id === userId).length || 0,
        total_spent: orderStats
          ?.filter(o => o.user_id === userId)
          .reduce((sum, o) => sum + parseFloat(o.total_amount), 0) || 0
      };
    });

    // Merge stats with users
    const usersWithStats = users.map(user => ({
      ...user,
      stats: userStats[user.id] || { total_reviews: 0, total_orders: 0, total_spent: 0 }
    }));

    res.json({
      users: usersWithStats,
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total: count,
        totalPages: Math.ceil(count / limit),
      },
    });
  } catch (error) {
    logger.error("Get users error:", error);
    res.status(500).json({ error: "Failed to fetch users" });
  }
};

const getAllOrders = async (req, res) => {
  try {
    const {
      page = 1,
      limit = 20,
      status,
      country,
      startDate,
      endDate,
    } = req.query;

    const offset = (page - 1) * limit;

    let query = supabaseAdmin.from("orders").select(
      `
                *,
                user:users(email),
                order_items(count)
            `,
      { count: "exact" }
    );

    if (status) {
      query = query.eq("status", status);
    }

    if (country) {
      query = query.eq("country", country);
    }

    if (startDate) {
      query = query.gte("created_at", startDate);
    }

    if (endDate) {
      query = query.lte("created_at", endDate);
    }

    const {
      data: orders,
      error,
      count,
    } = await query
      .range(offset, offset + limit - 1)
      .order("created_at", { ascending: false });

    if (error) {
      throw error;
    }

    res.json({
      orders,
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total: count,
        totalPages: Math.ceil(count / limit),
      },
    });
  } catch (error) {
    logger.error("Get all orders error:", error);
    res.status(500).json({ error: "Failed to fetch orders" });
  }
};

const createProduct = async (req, res) => {
  try {
    const productData = req.body;

    const { data: product, error } = await supabaseAdmin
      .from("products")
      .insert(productData)
      .select()
      .single();

    if (error) {
      throw error;
    }

    await logAudit(
      req.user.id,
      "PRODUCT_CREATE",
      "products",
      product.id,
      productData,
      req
    );

    res.status(201).json(product);
  } catch (error) {
    logger.error("Create product error:", error);
    res.status(500).json({ error: "Failed to create product" });
  }
};

const updateProduct = async (req, res) => {
  try {
    const { id } = req.params;
    const updateData = req.body;

    const { data: product, error } = await supabaseAdmin
      .from("products")
      .update(updateData)
      .eq("id", id)
      .select()
      .single();

    if (error || !product) {
      return res.status(404).json({ error: "Product not found" });
    }

    await logAudit(
      req.user.id,
      "PRODUCT_UPDATE",
      "products",
      id,
      updateData,
      req
    );

    res.json(product);
  } catch (error) {
    logger.error("Update product error:", error);
    res.status(500).json({ error: "Failed to update product" });
  }
};

const deleteProduct = async (req, res) => {
  try {
    const { id } = req.params;

    const { error } = await supabaseAdmin
      .from("products")
      .update({ is_active: false })
      .eq("id", id);

    if (error) {
      throw error;
    }

    await logAudit(req.user.id, "PRODUCT_DELETE", "products", id, {}, req);

    res.json({ message: "Product deleted successfully" });
  } catch (error) {
    logger.error("Delete product error:", error);
    res.status(500).json({ error: "Failed to delete product" });
  }
};

const createCategory = async (req, res) => {
  try {
    const { name, slug } = req.body;

    const { data: category, error } = await supabaseAdmin
      .from("categories")
      .insert({ name, slug })
      .select()
      .single();

    if (error) {
      throw error;
    }

    await logAudit(
      req.user.id,
      "CATEGORY_CREATE",
      "categories",
      category.id,
      { name, slug },
      req
    );

    res.status(201).json(category);
  } catch (error) {
    logger.error("Create category error:", error);
    res.status(500).json({ error: "Failed to create category" });
  }
};


const getAuditLogs = async (req, res) => {
  try {
    const {
      page = 1,
      limit = 50,
      userId,
      action,
      startDate,
      endDate,
    } = req.query;
    const offset = (page - 1) * limit;

    let query = supabaseAdmin.from("audit_logs").select(
      `
                *,
                user:users(email)
            `,
      { count: "exact" }
    );

    if (userId) {
      query = query.eq("user_id", userId);
    }

    if (action) {
      query = query.eq("action", action);
    }

    if (startDate) {
      query = query.gte("created_at", startDate);
    }

    if (endDate) {
      query = query.lte("created_at", endDate);
    }

    const {
      data: logs,
      error,
      count,
    } = await query
      .range(offset, offset + limit - 1)
      .order("created_at", { ascending: false });

    if (error) {
      throw error;
    }

    res.json({
      logs,
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total: count,
        totalPages: Math.ceil(count / limit),
      },
    });
  } catch (error) {
    logger.error("Get audit logs error:", error);
    res.status(500).json({ error: "Failed to fetch audit logs" });
  }
};
const toggleProductStatus = async (req, res) => {
    try {
        const { id } = req.params;
        
        const product = await db.Product.findByPk(id);
        
        if (!product) {
            return res.status(404).json({ error: 'Product not found' });
        }
        
        await product.update({
            is_active: !product.is_active
        });
        
        await logAudit(
            req.user.id,
            'PRODUCT_STATUS_TOGGLE',
            'products',
            id,
            { is_active: product.is_active },
            req
        );
        
        res.json({ 
            message: 'Product status updated',
            product 
        });
    } catch (error) {
        logger.error('Toggle product status error:', error);
        res.status(500).json({ error: 'Failed to update product status' });
    }
};


// Add these methods to backend/controllers/admin.controller.js

const getReviews = async (req, res) => {
  try {
    const {
      page = 1,
      limit = 20,
      search,
      rating,
      product,
      sortBy = 'created_at',
      sortOrder = 'desc'
    } = req.query;

    const offset = (page - 1) * limit;

    let query = supabaseAdmin
      .from('reviews')
      .select(`
        *,
        user:users(id, email),
        product:products(id, title, image_url)
      `, { count: 'exact' })
      .eq('is_deleted', false);

    if (search) {
      query = query.or(`comment.ilike.%${search}%`);
    }

    if (rating) {
      query = query.eq('rating', parseInt(rating));
    }

    if (product) {
      query = query.eq('product_id', product);
    }

    const { data: reviews, error, count } = await query
      .range(offset, offset + limit - 1)
      .order(sortBy, { ascending: sortOrder === 'asc' });

    if (error) {
      throw error;
    }

    res.json({
      reviews,
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total: count,
        totalPages: Math.ceil(count / limit)
      }
    });
  } catch (error) {
    logger.error('Get reviews error:', error);
    res.status(500).json({ error: 'Failed to fetch reviews' });
  }
};

const getReviewStats = async (req, res) => {
  try {
    // Get overall stats
    const { data: reviews, error: reviewsError } = await supabaseAdmin
      .from('reviews')
      .select('rating')
      .eq('is_deleted', false);

    if (reviewsError) throw reviewsError;

    const totalReviews = reviews.length;
    const averageRating = totalReviews > 0
      ? reviews.reduce((sum, r) => sum + r.rating, 0) / totalReviews
      : 0;

    // Get distribution
    const distribution = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };
    reviews.forEach(r => {
      distribution[r.rating]++;
    });

    // Get recent reviews
    const { data: recentReviews, error: recentError } = await supabaseAdmin
      .from('reviews')
      .select(`
        *,
        user:users(email),
        product:products(title)
      `)
      .eq('is_deleted', false)
      .order('created_at', { ascending: false })
      .limit(5);

    if (recentError) throw recentError;

    res.json({
      totalReviews,
      averageRating,
      distribution,
      recentReviews
    });
  } catch (error) {
    logger.error('Get review stats error:', error);
    res.status(500).json({ error: 'Failed to fetch review statistics' });
  }
};

const deleteReview = async (req, res) => {
  try {
    const { id } = req.params;

    // Get review details before deletion for audit log
    const { data: review, error: fetchError } = await supabaseAdmin
      .from("reviews")
      .select(`
        *,
        user:users(email),
        product:products(title)
      `)
      .eq("id", id)
      .single();

    if (fetchError || !review) {
      return res.status(404).json({ error: "Review not found" });
    }

    // Soft delete the review
    const { error } = await supabaseAdmin
      .from("reviews")
      .update({ is_deleted: true })
      .eq("id", id);

    if (error) {
      throw error;
    }

    await logAudit(req.user.id, "REVIEW_DELETE_ADMIN", "reviews", id, {
      user_email: review.user.email,
      product_title: review.product.title,
      rating: review.rating,
      comment: review.comment
    }, req);

    res.json({ message: "Review deleted successfully" });
  } catch (error) {
    logger.error("Delete review error:", error);
    res.status(500).json({ error: "Failed to delete review" });
  }
};


const updateCategory = async (req, res) => {
  try {
    const { id } = req.params;
    const { name, slug } = req.body;

    const { data: category, error } = await supabaseAdmin
      .from("categories")
      .update({ name, slug })
      .eq("id", id)
      .select()
      .single();

    if (error) {
      throw error;
    }

    await logAudit(
      req.user.id,
      "CATEGORY_UPDATE",
      "categories",
      category.id,
      { name, slug },
      req
    );

    res.json(category);
  } catch (error) {
    logger.error("Update category error:", error);
    res.status(500).json({ error: "Failed to update category" });
  }
};

const deleteCategory = async (req, res) => {
  try {
    const { id } = req.params;

    // Check if category has products
    const { count } = await supabaseAdmin
      .from("products")
      .select("id", { count: "exact", head: true })
      .eq("category_id", id);

    if (count > 0) {
      return res.status(400).json({ 
        error: "Cannot delete category with existing products" 
      });
    }

    const { error } = await supabaseAdmin
      .from("categories")
      .delete()
      .eq("id", id);

    if (error) {
      throw error;
    }

    await logAudit(req.user.id, "CATEGORY_DELETE", "categories", id, {}, req);

    res.json({ message: "Category deleted successfully" });
  } catch (error) {
    logger.error("Delete category error:", error);
    res.status(500).json({ error: "Failed to delete category" });
  }
};



module.exports = {
  getDashboardStats,
  getUsers,
  getAllOrders,
  createProduct,
  updateProduct,
  deleteProduct,
  createCategory,
  updateCategory,
  deleteCategory,
  toggleProductStatus,
  getAuditLogs,
  getReviews,
  getReviewStats,
  deleteReview
};
