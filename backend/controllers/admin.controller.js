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

    res.json({
      users,
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

const deleteReview = async (req, res) => {
  try {
    const { id } = req.params;

    const { error } = await supabaseAdmin
      .from("reviews")
      .update({ is_deleted: true })
      .eq("id", id);

    if (error) {
      throw error;
    }

    await logAudit(req.user.id, "REVIEW_DELETE_ADMIN", "reviews", id, {}, req);

    res.json({ message: "Review deleted successfully" });
  } catch (error) {
    logger.error("Delete review error:", error);
    res.status(500).json({ error: "Failed to delete review" });
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

module.exports = {
  getDashboardStats,
  getUsers,
  getAllOrders,
  createProduct,
  updateProduct,
  deleteProduct,
  createCategory,
  deleteReview,
  getAuditLogs,
};
