const express = require('express');
const { body } = require('express-validator');
const router = express.Router();

const orderController = require('../controllers/order.controller');
const { authenticate } = require('../middlewares/auth.middleware');
const validate = require('../middlewares/validation.middleware');

router.post('/checkout',
    authenticate,
    [
        body('items').isArray().withMessage('Items must be an array'),
        body('items.*.productId').isUUID().withMessage('Invalid product ID'),
        body('items.*.quantity').isInt({ min: 1 }).withMessage('Quantity must be at least 1')
    ],
    validate,
    orderController.createCheckoutSession
);

router.get('/', authenticate, orderController.getUserOrders);
router.get('/:id', authenticate, orderController.getOrder);
router.post('/:id/resend-codes', authenticate, orderController.resendOrderCodes);
router.post('/mock-checkout',
    authenticate,
    [
        body('items').isArray().withMessage('Items must be an array'),
        body('items.*.productId').isUUID().withMessage('Invalid product ID'),
        body('items.*.quantity').isInt({ min: 1 }).withMessage('Quantity must be at least 1')
    ],
    validate,
    orderController.createMockCheckout
);

module.exports = router;