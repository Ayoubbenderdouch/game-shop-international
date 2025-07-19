const { supabaseAdmin } = require('../config/supabase');
const logger = require('../utils/logger');
const { v4: uuidv4 } = require('uuid');

const uploadProductImage = async (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({ error: 'No image file provided' });
        }

        const file = req.file;
        const fileExt = file.originalname.split('.').pop();
        const fileName = `${uuidv4()}.${fileExt}`;
        const filePath = `products/${fileName}`;

        // Upload to Supabase storage
        const { data, error } = await supabaseAdmin.storage
            .from('product-images')
            .upload(filePath, file.buffer, {
                contentType: file.mimetype,
                cacheControl: '3600',
                upsert: false
            });

        if (error) {
            logger.error('Supabase upload error:', error);
            return res.status(500).json({ error: 'Failed to upload image' });
        }

        // Get public URL
        const { data: { publicUrl } } = supabaseAdmin.storage
            .from('product-images')
            .getPublicUrl(filePath);

        res.json({ 
            url: publicUrl,
            path: filePath,
            fileName
        });
    } catch (error) {
        logger.error('Upload product image error:', error);
        res.status(500).json({ error: 'Failed to upload image' });
    }
};

module.exports = { uploadProductImage };