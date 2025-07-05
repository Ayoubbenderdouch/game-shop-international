const { supabaseAdmin } = require("../config/supabase");
const logger = require("../utils/logger");

const checkLowStock = async () => {
  try {
    const { data: alerts, error } = await supabaseAdmin
      .from("stock_alerts")
      .select(
        `
                *,
                product:products(id, title, stock_count)
            `
      )
      .eq("is_active", true);

    if (error) {
      throw error;
    }

    for (const alert of alerts) {
      if (alert.product && alert.product.stock_count <= alert.threshold) {
        const lastAlertedTime = alert.last_alerted_at
          ? new Date(alert.last_alerted_at)
          : null;
        const now = new Date();

        if (!lastAlertedTime || now - lastAlertedTime > 24 * 60 * 60 * 1000) {
          await sendLowStockNotification(alert.product);

          await supabaseAdmin
            .from("stock_alerts")
            .update({ last_alerted_at: now.toISOString() })
            .eq("id", alert.id);
        }
      }
    }
  } catch (error) {
    logger.error("Check low stock error:", error);
  }
};

const sendLowStockNotification = async (product) => {
  logger.warn(
    `Low stock alert: Product "${product.title}" has only ${product.stock_count} items left`
  );

  const { data: admin } = await supabaseAdmin
    .from("users")
    .select("email")
    .eq("is_admin", true)
    .limit(1)
    .single();

  if (admin) {
    logger.info(
      `Low stock notification would be sent to ${admin.email} for product ${product.title}`
    );
  }
};

module.exports = {
  checkLowStock,
};
