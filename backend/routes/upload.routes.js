const express = require('express');
const multer = require('multer');
const router = express.Router();
const { authenticate, isAdmin } = require('../middlewares/auth.middleware');
const uploadController = require('../controllers/upload.controller');

// Configure multer for memory storage
const storage = multer.memoryStorage();
const upload = multer({ 
    storage,
    fileFilter: (req, file, cb) => {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (allowedTypes.includes(file.mimetype)) {
            cb(null, true);
        } else {
            cb(new Error('Invalid file type. Only JPEG, PNG, and WebP are allowed.'));
        }
    },
    limits: { fileSize: 5 * 1024 * 1024 } // 5MB limit
});

router.use(authenticate, isAdmin);

router.post('/product-image', upload.single('image'), uploadController.uploadProductImage);

module.exports = router;