const { supabaseAdmin } = require("../config/supabase");
const logger = require("../utils/logger");

const authenticate = async (req, res, next) => {
  try {
    const authHeader = req.headers.authorization;

    if (!authHeader) {
      return res.status(401).json({ error: "No authorization header" });
    }

    const token = authHeader.replace("Bearer ", "");

    if (!token) {
      return res.status(401).json({ error: "No token provided" });
    }

    const {
      data: { user },
      error,
    } = await supabaseAdmin.auth.getUser(token);

    if (error || !user) {
      logger.error("Token validation error:", error);
      return res.status(401).json({ error: "Invalid or expired token" });
    }

    // Check if email is confirmed
    if (!user.confirmed_at) {
      return res.status(401).json({ 
        error: "Email not confirmed",
        code: "EMAIL_NOT_CONFIRMED"
      });
    }

    // Try to get user profile
    const { data: userData, error: userError } = await supabaseAdmin
      .from("users")
      .select("*")
      .eq("id", user.id)
      .single();

    if (userError || !userData) {
      // For the profile endpoint, allow continuation without user profile
      // The controller will handle profile creation
      // Check the full path including the base route
      const isProfileEndpoint = req.originalUrl.includes('/auth/profile') || 
                               req.path.includes('/profile') ||
                               req.url.includes('/profile');
      
      if (isProfileEndpoint) {
        logger.info(`User profile not found for authenticated user: ${user.id}, will create in controller`);
        req.user = null;
        req.supabaseUser = user;
        return next();
      }
      
      // For other endpoints, user profile is required
      logger.error(`User profile not found for authenticated user: ${user.id} on path: ${req.originalUrl}`);
      return res.status(404).json({ error: "User profile not found" });
    }

    req.user = userData;
    req.supabaseUser = user;
    next();
  } catch (error) {
    logger.error("Authentication error:", error);
    res.status(500).json({ error: "Authentication failed" });
  }
};

const isAdmin = async (req, res, next) => {
  if (!req.user || !req.user.is_admin) {
    return res.status(403).json({ error: "Admin access required" });
  }
  next();
};

const optionalAuth = async (req, res, next) => {
  try {
    const authHeader = req.headers.authorization;

    if (!authHeader) {
      req.user = null;
      req.supabaseUser = null;
      return next();
    }

    const token = authHeader.replace("Bearer ", "");

    if (!token) {
      req.user = null;
      req.supabaseUser = null;
      return next();
    }

    const {
      data: { user },
      error,
    } = await supabaseAdmin.auth.getUser(token);

    if (!error && user && user.confirmed_at) {
      const { data: userData } = await supabaseAdmin
        .from("users")
        .select("*")
        .eq("id", user.id)
        .single();

      req.user = userData || null;
      req.supabaseUser = user;
    } else {
      req.user = null;
      req.supabaseUser = null;
    }

    next();
  } catch (error) {
    logger.error("Optional auth error:", error);
    req.user = null;
    req.supabaseUser = null;
    next();
  }
};

module.exports = { authenticate, isAdmin, optionalAuth };