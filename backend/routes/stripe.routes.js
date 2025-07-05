const express = require("express");
const router = express.Router();

const stripeController = require("../controllers/stripe.controller");
const { authenticate } = require("../middlewares/auth.middleware");

router.post("/webhook", stripeController.handleWebhook);
router.get(
  "/session/:sessionId",
  authenticate,
  stripeController.getCheckoutSession
);

module.exports = router;
