const express = require('express');
const { body } = require('express-validator');
const router = express.Router();

const reviewController = require('../controllers/review.controller');
const { authenticate, optionalAuth } = require('../middlewares/auth.middleware');
const validate = require('../middlewares/validation.middleware');

router.post('/',
    authenticate,
    [
        body('productId').isUUID().withMessage('Invalid product ID'),
        body('rating').isInt({ min: 1, max: 5 }).withMessage('Rating must be between 1 and 5'),
        body('comment').optional().isString().trim()
    ],
    validate,
    reviewController.createReview
);

router.put('/:id',
    authenticate,
    [
        body('rating').isInt({ min: 1, max: 5 }).withMessage('Rating must be between 1 and 5'),
        body('comment').optional().isString().trim()
    ],
    validate,
    reviewController.updateReview
);

router.delete('/:id', authenticate, reviewController.deleteReview);
router.get('/product/:productId', optionalAuth, reviewController.getProductReviews);
router.get('/my-reviews', authenticate, reviewController.getUserReviews);

module.exports = router;