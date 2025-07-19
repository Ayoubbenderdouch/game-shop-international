const express = require("express");
const cors = require("cors");
const helmet = require("helmet");
const morgan = require("morgan");
const dotenv = require("dotenv");
const cron = require("node-cron");

dotenv.config();

const authRoutes = require("./routes/auth.routes");
const productRoutes = require("./routes/product.routes");
const orderRoutes = require("./routes/order.routes");
const reviewRoutes = require("./routes/review.routes");
const adminRoutes = require("./routes/admin.routes");
const stockRoutes = require("./routes/stock.routes");
// const stripeRoutes = require("./routes/stripe.routes");
const uploadRoutes = require('./routes/upload.routes');

const errorHandler = require("./middlewares/errorHandler.middleware");
const rateLimiter = require("./middlewares/rateLimiter.middleware");
const logger = require("./utils/logger");
const { checkLowStock } = require("./services/stock.service");

const app = express();
const PORT = process.env.PORT || 3000;

app.use(helmet());
app.use(
  cors({
    origin: [process.env.FRONTEND_URL, process.env.ADMIN_DASHBOARD_URL],
    credentials: true,
  })
);

app.use(
  morgan("combined", {
    stream: { write: (message) => logger.info(message.trim()) },
  })
);

// app.use("/api/stripe/webhook", express.raw({ type: "application/json" }));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

app.use(rateLimiter);

app.use("/api/auth", authRoutes);
app.use("/api/products", productRoutes);
app.use("/api/orders", orderRoutes);
app.use("/api/reviews", reviewRoutes);
app.use("/api/admin", adminRoutes);
app.use("/api/stock", stockRoutes);
app.use('/api/upload', uploadRoutes);

// app.use("/api/stripe", stripeRoutes);

app.get("/health", (req, res) => {
  res.json({ status: "OK", timestamp: new Date().toISOString() });
});

app.use(errorHandler);

cron.schedule("0 */6 * * *", async () => {
  logger.info("Running stock check cron job");
  await checkLowStock();
});

app.listen(PORT, () => {
  logger.info(`Server is running on port ${PORT}`);
});
