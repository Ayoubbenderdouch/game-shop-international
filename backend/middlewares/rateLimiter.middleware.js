const rateLimit = require("express-rate-limit");

const createRateLimiter = (windowMs = 15 * 60 * 1000, max = 100) => {
  return rateLimit({
    windowMs,
    max,
    message: "Too many requests from this IP, please try again later.",
    standardHeaders: true,
    legacyHeaders: false,
  });
};

const authLimiter = createRateLimiter(15 * 60 * 1000, 5);
const generalLimiter = createRateLimiter(15 * 60 * 1000, 100);
const strictLimiter = createRateLimiter(15 * 60 * 1000, 10);

module.exports = generalLimiter;
module.exports.authLimiter = authLimiter;
module.exports.strictLimiter = strictLimiter;
