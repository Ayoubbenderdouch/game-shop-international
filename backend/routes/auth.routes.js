const express = require("express");
const { body } = require("express-validator");
const router = express.Router();

const authController = require("../controllers/auth.controller");
const { authenticate } = require("../middlewares/auth.middleware");
const validate = require("../middlewares/validation.middleware");
const { authLimiter } = require("../middlewares/rateLimiter.middleware");

router.post(
  "/register",
  authLimiter,
  [
    body("email").isEmail().normalizeEmail(),
    body("password")
      .isLength({ min: 6 })
      .withMessage("Password must be at least 6 characters"),
  ],
  validate,
  authController.register
);

router.post(
  "/login",
  authLimiter,
  [body("email").isEmail().normalizeEmail(), body("password").notEmpty()],
  validate,
  authController.login
);

router.post("/logout", authenticate, authController.logout);

router.get("/profile", authenticate, authController.getProfile);

// Debug route - only for development
router.get("/debug", authenticate, authController.debugAuth);

// Add resend verification email route
router.post(
  "/resend-verification",
  authLimiter,
  [body("email").isEmail().normalizeEmail()],
  validate,
  authController.resendVerification
);

router.get("/confirm-email", authController.confirmEmail);

module.exports = router;
