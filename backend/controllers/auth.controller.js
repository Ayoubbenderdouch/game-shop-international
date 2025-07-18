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
      await logAudit(req.user.id, "USER_LOGOUT", "users", req.user.id, {}, req);
    }

    res.json({ message: "Logged out successfully" });
  } catch (error) {
    logger.error("Logout error:", error);
    res.status(500).json({ error: "Logout failed" });
  }
};

// Add this updated getProfile function to your auth.controller.js

const getProfile = async (req, res) => {
  try {
    // Handle case where user profile doesn't exist yet (from middleware)
    if (!req.user && req.supabaseUser) {
      // Create profile for authenticated user who doesn't have one yet
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
        // Check if user already exists (race condition)
        if (createError.code === "23505") {
          // Unique violation
          const { data: existingUser, error: fetchError } = await supabaseAdmin
            .from("users")
            .select("*")
            .eq("id", req.supabaseUser.id)
            .single();

          if (!fetchError && existingUser) {
            return res.json({
              ...existingUser,
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
        return res.status(400).json({ error: "Failed to create user profile" });
      }

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
      return res.status(404).json({ error: "User profile not found" });
    }

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

module.exports = {
  register,
  login,
  logout,
  getProfile,
  resendVerification,
  confirmEmail,
};
