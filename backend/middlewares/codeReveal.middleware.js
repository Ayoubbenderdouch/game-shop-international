// backend/middlewares/codeReveal.middleware.js
const rateLimit = require('express-rate-limit');
const { supabaseAdmin } = require('../config/supabase');
const logger = require('../utils/logger');

// Rate limiter for code reveals - 10 reveals per hour per user
const codeRevealLimiter = rateLimit({
  windowMs: 60 * 60 * 1000, // 1 hour
  max: 10, // limit each IP/user to 10 requests per windowMs
  message: 'Too many code reveal requests, please try again later.',
  standardHeaders: true,
  legacyHeaders: false,
  keyGenerator: (req) => req.user?.id || req.ip, // Use user ID if authenticated, otherwise IP
});

// Middleware to log code reveal attempts
const logCodeReveal = async (req, res, next) => {
  try {
    const { orderId, itemId } = req.params;
    const userId = req.user.id;
    
    // Log the code reveal attempt
    await supabaseAdmin
      .from('audit_logs')
      .insert({
        user_id: userId,
        action: 'CODE_REVEAL',
        entity_type: 'order_items',
        entity_id: itemId,
        details: {
          order_id: orderId,
          timestamp: new Date().toISOString(),
          ip: req.ip,
          user_agent: req.headers['user-agent']
        },
        ip_address: req.ip,
        user_agent: req.headers['user-agent']
      });
      
    next();
  } catch (error) {
    logger.error('Error logging code reveal:', error);
    next(); // Continue even if logging fails
  }
};

// Middleware to verify order ownership
const verifyOrderOwnership = async (req, res, next) => {
  try {
    const { orderId } = req.params;
    const userId = req.user.id;
    
    const { data: order, error } = await supabaseAdmin
      .from('orders')
      .select('id, user_id, status')
      .eq('id', orderId)
      .single();
      
    if (error || !order) {
      return res.status(404).json({ error: 'Order not found' });
    }
    
    if (order.user_id !== userId) {
      logger.warn(`Unauthorized access attempt to order ${orderId} by user ${userId}`);
      return res.status(403).json({ error: 'Unauthorized access to order' });
    }
    
    if (order.status !== 'completed') {
      return res.status(400).json({ error: 'Order not completed yet' });
    }
    
    req.order = order;
    next();
  } catch (error) {
    logger.error('Error verifying order ownership:', error);
    res.status(500).json({ error: 'Failed to verify order' });
  }
};

module.exports = {
  codeRevealLimiter,
  logCodeReveal,
  verifyOrderOwnership
};