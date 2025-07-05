const express = require('express');
const { body } = require('express-validator');
const multer = require('multer');
const path = require('path');
const router = express.Router();

const stockController = require('../controllers/stock.controller');
const { authenticate, isAdmin } = require('../middlewares/auth.middleware');
const validate = require('../middlewares/validation.middleware');

const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, 'uploads/');
    },
    filename: (req, file, cb) => {
        cb(null, Date.now() + path.extname(file.originalname));
    }
});

const upload = multer({ 
    storage,
    fileFilter: (req, file, cb) => {
        const allowedTypes = ['.csv', '.xlsx', '.xls'];
        const ext = path.extname(file.originalname).toLowerCase();
        if (allowedTypes.includes(ext)) {
            cb(null, true);
        } else {
            cb(new Error('Invalid file type'));
        }
    },
    limits: { fileSize: 10 * 1024 * 1024 }
});

router.use(authenticate, isAdmin);

router.post('/codes/single',
    [
        body('productId').isUUID(),
        body('code').notEmpty().trim(),
        body('expiresAt').optional().isISO8601()
    ],
    validate,
    stockController.addSingleCode
);

router.post('/codes/bulk',
    upload.single('file'),
    [
        body('productId').isUUID()
    ],
    validate,
    stockController.bulkAddCodes
);

router.get('/product/:productId', stockController.getProductStock);
router.delete('/codes/:id', stockController.deleteCode);

router.post('/alerts',
    [
        body('productId').isUUID(),
        body('threshold').isInt({ min: 1 })
    ],
    validate,
    stockController.setStockAlert
);

module.exports = router;