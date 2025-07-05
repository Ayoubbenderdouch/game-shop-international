const express = require('express');
const { body } = require('express-validator');
const router = express.Router();

const adminController = require('../controllers/admin.controller');
const { authenticate, isAdmin } = require('../middlewares/auth.middleware');
const validate = require('../middlewares/validation.middleware');

router.use(authenticate, isAdmin);

router.get('/dashboard', adminController.getDashboardStats);
router.get('/users', adminController.getUsers);
router.get('/orders', adminController.getAllOrders);
router.get('/audit-logs', adminController.getAuditLogs);

router.post('/products',
    [
        body('title').notEmpty().trim(),
        body('type').isIn(['game_card', 'gift_card', 'subscription', 'uc_topup']),
        body('price').isFloat({ min: 0 }),
        body('category_id').isUUID(),
        body('description').optional().trim(),
        body('image_url').optional().isURL(),
        body('tags').optional().isArray(),
        body('country_availability').optional().isArray(),
        body('is_active').optional().isBoolean(),
        body('is_preorder').optional().isBoolean()
    ],
    validate,
    adminController.createProduct
);

router.put('/products/:id',
    [
        body('title').optional().notEmpty().trim(),
        body('type').optional().isIn(['game_card', 'gift_card', 'subscription', 'uc_topup']),
        body('price').optional().isFloat({ min: 0 }),
        body('category_id').optional().isUUID(),
        body('description').optional().trim(),
        body('image_url').optional().isURL(),
        body('tags').optional().isArray(),
        body('country_availability').optional().isArray(),
        body('is_active').optional().isBoolean(),
        body('is_preorder').optional().isBoolean()
    ],
    validate,
    adminController.updateProduct
);

router.delete('/products/:id', adminController.deleteProduct);

router.post('/categories',
    [
        body('name').notEmpty().trim(),
        body('slug').notEmpty().trim().matches(/^[a-z0-9-]+$/)
    ],
    validate,
    adminController.createCategory
);

router.delete('/reviews/:id', adminController.deleteReview);

module.exports = router;