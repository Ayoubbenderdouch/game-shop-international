const express = require('express');
const router = express.Router();

const productController = require('../controllers/product.controller');
const { optionalAuth } = require('../middlewares/auth.middleware');

router.get('/', optionalAuth, productController.getProducts);
router.get('/categories', productController.getCategories);
router.get('/category/:slug', optionalAuth, productController.getProductsByCategory);
router.get('/:id', optionalAuth, productController.getProduct);

module.exports = router;