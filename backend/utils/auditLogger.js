const { supabaseAdmin } = require("../config/supabase");
const { getClientIP } = require("./geolocation");
const logger = require("./logger");

const logAudit = async (userId, action, entityType, entityId, details, req) => {
  try {
    const auditData = {
      user_id: userId,
      action,
      entity_type: entityType,
      entity_id: entityId,
      details,
      ip_address: getClientIP(req),
      user_agent: req.headers["user-agent"],
    };

    const { error } = await supabaseAdmin.from("audit_logs").insert(auditData);

    if (error) {
      logger.error("Failed to create audit log:", error);
    }
  } catch (error) {
    logger.error("Audit logging error:", error);
  }
};

module.exports = { logAudit };
