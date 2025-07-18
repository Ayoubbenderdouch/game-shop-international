const { supabase, supabaseAdmin } = require("../config/supabase");
const { getCountryFromIP, getClientIP } = require("../utils/geolocation");
const { logAudit } = require("../utils/auditLogger");
const logger = require("../utils/logger");

const register = async (req, res) => {
  try {
    const { email, password } = req.body;
    const country = getCountryFromIP(getClientIP(req));

    // Get the origin from request headers for redirect URL
    const origin = req.headers.origin || "http://localhost:5173";

    const { data: authData, error: authError } = await supabase.auth.signUp({
      email,
      password,
      options: {
        emailRedirectTo: `${origin}/auth/confirm`,
        data: {
          country: country,
        },
      },
    });

    if (authError) {
      return res.status(400).json({ error: authError.message });
    }

    // Always require email confirmation
    res.status(201).json({
      message: "Please check your email to confirm your account",
      requiresEmailConfirmation: true,
      email: email,
    });
  } catch (error) {
    logger.error("Registration error:", error);
    res.status(500).json({ error: "Registration failed" });
  }
};

const login = async (req, res) => {
  try {
    const { email, password } = req.body;

    const { data: authData, error: authError } =
      await supabase.auth.signInWithPassword({
        email,
        password,
      });

    if (authError) {
      // Check if email not confirmed
      if (authError.message.includes("Email not confirmed")) {
        return res.status(401).json({
          error: "Please confirm your email before logging in",
          code: "EMAIL_NOT_CONFIRMED",
        });
      }
      return res.status(401).json({ error: "Invalid credentials" });
    }

    // Double-check email confirmation
    if (!authData.user.confirmed_at) {
      return res.status(401).json({
        error: "Please confirm your email before logging in",
        code: "EMAIL_NOT_CONFIRMED",
      });
    }

    // Try to get existing user profile
    const { data: userData, error: userError } = await supabaseAdmin
      .from("users")
      .select("*")
      .eq("id", authData.user.id)
      .single();

    if (userError || !userData) {
      // Create user profile if doesn't exist (first login after confirmation)
      const country = getCountryFromIP(getClientIP(req));
      const isFirstUser = await checkFirstUser();

      const { data: newUserData, error: createError } = await supabaseAdmin
        .from("users")
        .insert({
          id: authData.user.id,
          email,
          country,
          is_admin: isFirstUser || email === process.env.ADMIN_EMAIL,
        })
        .select()
        .single();

      if (createError) {
        logger.error("Failed to create user profile:", createError);
        return res.status(400).json({ error: "Failed to create user profile" });
      }

      await logAudit(
        authData.user.id,
        "USER_FIRST_LOGIN",
        "users",
        authData.user.id,
        { email },
        req
      );

      return res.json({
        user: newUserData,
        session: authData.session,
      });
    }

    // Update country if changed
    const country = getCountryFromIP(getClientIP(req));
    if (userData.country !== country) {
      await supabaseAdmin
        .from("users")
        .update({ country })
        .eq("id", authData.user.id);
    }

    await logAudit(
      authData.user.id,
      "USER_LOGIN",
      "users",
      authData.user.id,
      { email },
      req
    );

    res.json({
      user: userData,
      session: authData.session,
    });
  } catch (error) {
    logger.error("Login error:", error);
    res.status(500).json({ error: "Login failed" });
  }
};

const logout = async (req, res) => {
  try {
    const token = req.headers.authorization?.replace("Bearer ", "");

    if (token) {
      await supabase.auth.signOut();
      if (req.user?.id) {
        await logAudit(
          req.user.id,
          "USER_LOGOUT",
          "users",
          req.user.id,
          {},
          req
        );
      }
    }

    res.json({ message: "Logged out successfully" });
  } catch (error) {
    logger.error("Logout error:", error);
    res.status(500).json({ error: "Logout failed" });
  }
};

const getProfile = async (req, res) => {
  try {
    logger.info(
      `Getting profile for user: ${req.supabaseUser?.id || "unknown"}`
    );

    // If user is authenticated via Supabase but no profile exists
    if (!req.user && req.supabaseUser) {
      logger.info(
        `No profile found in middleware for user ${req.supabaseUser.id}, checking database...`
      );

      // First, check if profile already exists (in case of race condition)
      const { data: existingUser, error: fetchError } = await supabaseAdmin
        .from("users")
        .select("*")
        .eq("id", req.supabaseUser.id)
        .single();

      if (fetchError && fetchError.code !== "PGRST116") {
        logger.error(
          `Error fetching user profile: ${fetchError.message}`,
          fetchError
        );
        return res.status(500).json({ error: "Database error" });
      }

      if (existingUser) {
        logger.info(`Found existing profile for user ${req.supabaseUser.id}`);
        // Profile exists, return it
        const { data: orders } = await supabaseAdmin
          .from("orders")
          .select("id")
          .eq("user_id", existingUser.id)
          .eq("status", "completed");

        const { data: reviews } = await supabaseAdmin
          .from("reviews")
          .select("id")
          .eq("user_id", existingUser.id)
          .eq("is_deleted", false);

        return res.json({
          ...existingUser,
          stats: {
            total_orders: orders?.length || 0,
            total_reviews: reviews?.length || 0,
          },
        });
      }

      logger.info(`Creating new profile for user ${req.supabaseUser.id}`);
      // Profile doesn't exist, create it
      const country = getCountryFromIP(getClientIP(req));
      const isFirstUser = await checkFirstUser();

      const { data: newUserData, error: createError } = await supabaseAdmin
        .from("users")
        .insert({
          id: req.supabaseUser.id,
          email: req.supabaseUser.email,
          country,
          is_admin:
            isFirstUser || req.supabaseUser.email === process.env.ADMIN_EMAIL,
        })
        .select()
        .single();

      if (createError) {
        // Check if it's a unique constraint violation (profile was created by another request)
        if (createError.code === "23505") {
          logger.info(
            `Profile already exists (race condition) for user ${req.supabaseUser.id}`
          );
          // Try to fetch the profile again
          const { data: retryUser, error: retryError } = await supabaseAdmin
            .from("users")
            .select("*")
            .eq("id", req.supabaseUser.id)
            .single();

          if (retryUser && !retryError) {
            return res.json({
              ...retryUser,
              stats: {
                total_orders: 0,
                total_reviews: 0,
              },
            });
          }
        }

        logger.error(
          "Failed to create user profile in getProfile:",
          createError
        );
        return res.status(500).json({ error: "Failed to create user profile" });
      }

      await logAudit(
        req.supabaseUser.id,
        "USER_PROFILE_CREATED",
        "users",
        req.supabaseUser.id,
        { email: req.supabaseUser.email },
        req
      );

      return res.json({
        ...newUserData,
        stats: {
          total_orders: 0,
          total_reviews: 0,
        },
      });
    }

    // Normal case - user profile exists
    if (!req.user) {
      logger.error("No user found in request");
      return res.status(404).json({ error: "User profile not found" });
    }

    logger.info(`Returning existing profile for user ${req.user.id}`);
    // Get user stats
    const { data: orders } = await supabaseAdmin
      .from("orders")
      .select("id")
      .eq("user_id", req.user.id)
      .eq("status", "completed");

    const { data: reviews } = await supabaseAdmin
      .from("reviews")
      .select("id")
      .eq("user_id", req.user.id)
      .eq("is_deleted", false);

    res.json({
      ...req.user,
      stats: {
        total_orders: orders?.length || 0,
        total_reviews: reviews?.length || 0,
      },
    });
  } catch (error) {
    logger.error("Get profile error:", error);
    res.status(500).json({ error: "Failed to get profile" });
  }
};

const checkFirstUser = async () => {
  const { count } = await supabaseAdmin
    .from("users")
    .select("*", { count: "exact", head: true });

  return count === 0;
};

const resendVerification = async (req, res) => {
  try {
    const { email } = req.body;

    if (!email) {
      return res.status(400).json({ error: "Email is required" });
    }

    const origin = req.headers.origin || "http://localhost:5173";

    const { error } = await supabase.auth.resend({
      type: "signup",
      email: email,
      options: {
        emailRedirectTo: `${origin}/auth/confirm`,
      },
    });

    if (error) {
      logger.error("Resend verification error:", error);
      return res
        .status(400)
        .json({ error: "Failed to resend verification email" });
    }

    res.json({ message: "Verification email sent successfully" });
  } catch (error) {
    logger.error("Resend verification error:", error);
    res.status(500).json({ error: "Failed to resend verification email" });
  }
};

const confirmEmail = async (req, res) => {
  try {
    const { token_hash, type } = req.query;

    if (!token_hash || type !== "email") {
      return res.status(400).json({ error: "Invalid confirmation link" });
    }

    const { data, error } = await supabase.auth.verifyOtp({
      token_hash,
      type: "email",
    });

    if (error) {
      return res
        .status(400)
        .json({ error: "Invalid or expired confirmation link" });
    }

    res.json({ message: "Email confirmed successfully" });
  } catch (error) {
    logger.error("Email confirmation error:", error);
    res.status(500).json({ error: "Confirmation failed" });
  }
};

// Debug endpoint
const debugAuth = async (req, res) => {
  try {
    const token = req.headers.authorization?.replace("Bearer ", "");

    logger.info("Debug auth endpoint called");

    // Step 1: Validate the token
    const {
      data: { user: supabaseUser },
      error: authError,
    } = await supabaseAdmin.auth.getUser(token);

    if (authError) {
      return res.json({
        step: "Token validation",
        error: authError.message,
        success: false,
      });
    }

    logger.info(`Token valid for user: ${supabaseUser.id}`);

    // Step 2: Try to fetch user from database with detailed logging
    logger.info(`Attempting to fetch user ${supabaseUser.id} from database...`);

    const { data: dbUser, error: dbError } = await supabaseAdmin
      .from("users")
      .select("*")
      .eq("id", supabaseUser.id)
      .single();

    if (dbError) {
      logger.error(`Database error: ${dbError.message}`, dbError);
    } else {
      logger.info(`Database user found: ${JSON.stringify(dbUser)}`);
    }

    // Step 3: Check all users (for debugging)
    const { data: allUsers, error: allUsersError } = await supabaseAdmin
      .from("users")
      .select("id, email")
      .limit(10);

    // Step 4: Test a direct query with the user ID
    const directQuery = await supabaseAdmin
      .from("users")
      .select("*")
      .eq("id", supabaseUser.id);

    res.json({
      supabaseUser: {
        id: supabaseUser.id,
        email: supabaseUser.email,
        confirmed_at: supabaseUser.confirmed_at,
      },
      dbUser: dbUser || null,
      dbError: dbError
        ? {
            message: dbError.message,
            code: dbError.code,
            details: dbError.details,
          }
        : null,
      allUsers: allUsers || [],
      allUsersError: allUsersError?.message || null,
      directQuery: directQuery.data || null,
      directQueryError: directQuery.error?.message || null,
      timestamp: new Date().toISOString(),
    });
  } catch (error) {
    logger.error("Debug endpoint error:", error);
    res.json({
      error: error.message,
      stack: error.stack,
    });
  }
};

module.exports = {
  register,
  login,
  logout,
  getProfile,
  resendVerification,
  confirmEmail,
  debugAuth,
  checkFirstUser,
};
