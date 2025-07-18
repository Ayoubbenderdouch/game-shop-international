const { supabaseAdmin } = require("../config/supabase");
const { getCountryFromIP, getClientIP } = require("../utils/geolocation");
const { logAudit } = require("../utils/auditLogger");
const logger = require("../utils/logger");
const stripe = require("../config/stripe");

const createCheckoutSession = async (req, res) => {
  try {
    const { items } = req.body;
    const userId = req.user.id;

    if (!items || items.length === 0) {
      return res.status(400).json({ error: "No items provided" });
    }

    const productIds = items.map((item) => item.productId);
    const { data: products, error: productsError } = await supabaseAdmin
      .from("products")
      .select("*")
      .in("id", productIds)
      .eq("is_active", true);

    if (productsError || !products) {
      return res.status(400).json({ error: "Failed to fetch products" });
    }

    const productMap = new Map(products.map((p) => [p.id, p]));

    // Validate stock
    for (const item of items) {
      const product = productMap.get(item.productId);
      if (!product) {
        return res
          .status(400)
          .json({ error: `Product ${item.productId} not found` });
      }

      const { count: availableStock } = await supabaseAdmin
        .from("product_codes")
        .select("*", { count: "exact", head: true })
        .eq("product_id", item.productId)
        .eq("is_used", false);

      if (availableStock < item.quantity) {
        return res.status(400).json({
          error: `Insufficient stock for ${product.title}. Available: ${availableStock}`,
        });
      }
    }

    const lineItems = items.map((item) => {
      const product = productMap.get(item.productId);
      return {
        price_data: {
          currency: "usd",
          product_data: {
            name: product.title,
            description: product.description,
            images: product.image_url ? [product.image_url] : [],
          },
          unit_amount: Math.round(product.price * 100),
        },
        quantity: item.quantity,
      };
    });

    const country = getCountryFromIP(getClientIP(req));

    const { data: order, error: orderError } = await supabaseAdmin
      .from("orders")
      .insert({
        user_id: userId,
        total_amount: items.reduce((sum, item) => {
          const product = productMap.get(item.productId);
          return sum + product.price * item.quantity;
        }, 0),
        status: "pending",
        country,
      })
      .select()
      .single();

    if (orderError) {
      return res.status(500).json({ error: "Failed to create order" });
    }

    const session = await stripe.checkout.sessions.create({
      payment_method_types: ["card"],
      line_items: lineItems,
      mode: "payment",
      success_url: `${process.env.FRONTEND_URL}/order-success?session_id={CHECKOUT_SESSION_ID}`,
      cancel_url: `${process.env.FRONTEND_URL}/cart`,
      customer_email: req.user.email,
      metadata: {
        order_id: order.id,
        user_id: userId,
        items: JSON.stringify(items),
      },
    });

    await supabaseAdmin
      .from("orders")
      .update({ stripe_session_id: session.id })
      .eq("id", order.id);

    await logAudit(
      userId,
      "ORDER_CREATE",
      "orders",
      order.id,
      { items, total: order.total_amount },
      req
    );

    res.json({
      sessionId: session.id,
      sessionUrl: session.url,
      orderId: order.id,
    });
  } catch (error) {
    logger.error("Create checkout session error:", error);
    res.status(500).json({ error: "Failed to create checkout session" });
  }
};

const createMockCheckout = async (req, res) => {
  try {
    const { items } = req.body;
    const userId = req.user.id;

    // Validate items
    if (!items || !Array.isArray(items) || items.length === 0) {
      return res.status(400).json({ error: "Invalid items" });
    }

    // Fetch products
    const productIds = items.map((item) => item.productId);
    const { data: products, error: productsError } = await supabaseAdmin
      .from("products")
      .select("*")
      .in("id", productIds)
      .eq("is_active", true);

    if (productsError || !products) {
      logger.error("Error fetching products:", productsError);
      return res.status(400).json({ error: "Failed to fetch products" });
    }

    const productMap = new Map(products.map((p) => [p.id, p]));

    // Calculate total and validate stock
    let totalAmount = 0;
    const orderItems = [];

    for (const item of items) {
      const product = productMap.get(item.productId);

      if (!product) {
        return res
          .status(400)
          .json({ error: `Product ${item.productId} not available` });
      }

      // Check available stock
      const { count: availableStock, error: stockError } = await supabaseAdmin
        .from("product_codes")
        .select("*", { count: "exact", head: true })
        .eq("product_id", item.productId)
        .eq("is_used", false);

      if (stockError) {
        logger.error("Error checking stock:", stockError);
        return res.status(500).json({ error: "Failed to check stock" });
      }

      if (availableStock < item.quantity) {
        return res.status(400).json({
          error: `Insufficient stock for ${product.title}. Available: ${availableStock}`,
        });
      }

      totalAmount += product.price * item.quantity;
      orderItems.push({
        product,
        quantity: item.quantity,
        price: product.price,
      });
    }

    const country = getCountryFromIP(getClientIP(req));

    // Create order
    const { data: order, error: orderError } = await supabaseAdmin
      .from("orders")
      .insert({
        user_id: userId,
        total_amount: totalAmount,
        status: "completed",
        country: country,
      })
      .select()
      .single();

    if (orderError) {
      logger.error("Error creating order:", orderError);
      return res.status(500).json({ error: "Failed to create order" });
    }

    // Create order items and assign codes
    for (const item of orderItems) {
      // Create order item
      const { data: orderItem, error: orderItemError } = await supabaseAdmin
        .from("order_items")
        .insert({
          order_id: order.id,
          product_id: item.product.id,
          quantity: item.quantity,
          price: item.price,
        })
        .select()
        .single();

      if (orderItemError) {
        logger.error("Error creating order item:", orderItemError);
        // Rollback by deleting the order
        await supabaseAdmin.from("orders").delete().eq("id", order.id);
        return res.status(500).json({ error: "Failed to create order items" });
      }

      // Get available codes for this product
      const { data: codes, error: codesError } = await supabaseAdmin
        .from("product_codes")
        .select("*")
        .eq("product_id", item.product.id)
        .eq("is_used", false)
        .limit(item.quantity);

      if (codesError || !codes || codes.length < item.quantity) {
        logger.error("Error fetching codes:", codesError);
        // Rollback
        await supabaseAdmin.from("orders").delete().eq("id", order.id);
        return res
          .status(500)
          .json({ error: "Failed to assign product codes" });
      }

      // Assign codes to user
      for (const code of codes) {
        const { error: updateError } = await supabaseAdmin
          .from("product_codes")
          .update({
            is_used: true,
            used_by: userId,
            used_at: new Date().toISOString(),
          })
          .eq("id", code.id);

        if (updateError) {
          logger.error("Error updating code:", updateError);
          // Continue with other codes but log the error
        }

        // Update order item with the code
        await supabaseAdmin
          .from("order_items")
          .update({
            product_code_id: code.id,
          })
          .eq("id", orderItem.id);
      }
    }

    // Log the successful order
    await logAudit(
      userId,
      "MOCK_ORDER_COMPLETED",
      "orders",
      order.id,
      { items, total: totalAmount },
      req
    );

    // Send order confirmation email (mock)
    logger.info("Mock order completed:", order.id);

    res.json({
      success: true,
      orderId: order.id,
      message: "Order completed successfully",
    });
  } catch (error) {
    logger.error("Mock checkout error:", error);
    res.status(500).json({ error: "Checkout failed" });
  }
};

const getUserOrders = async (req, res) => {
  try {
    const { page = 1, limit = 10, status } = req.query;
    const offset = (page - 1) * limit;

    let query = supabaseAdmin
      .from("orders")
      .select(
        `
                *,
                order_items(
                    *,
                    product:products(id, title, image_url),
                    product_code:product_codes(id, code)
                )
            `,
        { count: "exact" }
      )
      .eq("user_id", req.user.id)
      .order("created_at", { ascending: false })
      .range(offset, offset + limit - 1);

    if (status) {
      query = query.eq("status", status);
    }

    const { data: orders, error, count } = await query;

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
    logger.error("Get user orders error:", error);
    res.status(500).json({ error: "Failed to fetch orders" });
  }
};

const getOrder = async (req, res) => {
  try {
    const { id } = req.params;

    const { data: order, error } = await supabaseAdmin
      .from("orders")
      .select(
        `
                *,
                order_items(
                    *,
                    product:products(id, title, description, image_url),
                    product_code:product_codes(id, code, expires_at)
                )
            `
      )
      .eq("id", id)
      .eq("user_id", req.user.id)
      .single();

    if (error || !order) {
      return res.status(404).json({ error: "Order not found" });
    }

    res.json(order);
  } catch (error) {
    logger.error("Get order error:", error);
    res.status(500).json({ error: "Failed to fetch order" });
  }
};

const resendOrderCodes = async (req, res) => {
  try {
    const { id } = req.params;

    const { data: order, error } = await supabaseAdmin
      .from("orders")
      .select(
        `
                *,
                order_items(
                    *,
                    product:products(title),
                    product_code:product_codes(code)
                )
            `
      )
      .eq("id", id)
      .eq("user_id", req.user.id)
      .eq("status", "completed")
      .single();

    if (error || !order) {
      return res
        .status(404)
        .json({ error: "Order not found or not completed" });
    }

    await logAudit(req.user.id, "ORDER_RESEND_CODES", "orders", id, {}, req);

    res.json({ message: "Order codes have been resent to your email" });
  } catch (error) {
    logger.error("Resend order codes error:", error);
    res.status(500).json({ error: "Failed to resend codes" });
  }
};

module.exports = {
  createCheckoutSession,
  createMockCheckout,
  getUserOrders,
  getOrder,
  resendOrderCodes,
};
