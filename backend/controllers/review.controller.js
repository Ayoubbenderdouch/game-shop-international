const { supabaseAdmin } = require('../config/supabase');
const { logAudit } = require('../utils/auditLogger');
const logger = require('../utils/logger');

const createReview = async (req, res) => {
    try {
        const { productId, rating, comment } = req.body;
        const userId = req.user.id;

        const { data: purchase, error: purchaseError } = await supabaseAdmin
            .from('order_items')
            .select(`
                id,
                order:orders!inner(user_id, status)
            `)
            .eq('product_id', productId)
            .eq('order.user_id', userId)
            .eq('order.status', 'completed')
            .limit(1)
            .single();

        if (purchaseError || !purchase) {
            return res.status(403).json({ error: 'You must purchase this product before reviewing' });
        }

        const { data: existingReview } = await supabaseAdmin
            .from('reviews')
            .select('id')
            .eq('user_id', userId)
            .eq('product_id', productId)
            .single();

        if (existingReview) {
            return res.status(400).json({ error: 'You have already reviewed this product' });
        }

        const { data: review, error: reviewError } = await supabaseAdmin
            .from('reviews')
            .insert({
                user_id: userId,
                product_id: productId,
                order_item_id: purchase.id,
                rating,
                comment
            })
            .select()
            .single();

        if (reviewError) {
            throw reviewError;
        }

        await logAudit(userId, 'REVIEW_CREATE', 'reviews', review.id, { productId, rating }, req);

        res.status(201).json(review);
    } catch (error) {
        logger.error('Create review error:', error);
        res.status(500).json({ error: 'Failed to create review' });
    }
};

const updateReview = async (req, res) => {
    try {
        const { id } = req.params;
        const { rating, comment } = req.body;
        const userId = req.user.id;

        const { data: review, error: reviewError } = await supabaseAdmin
            .from('reviews')
            .update({ rating, comment })
            .eq('id', id)
            .eq('user_id', userId)
            .eq('is_deleted', false)
            .select()
            .single();

        if (reviewError || !review) {
            return res.status(404).json({ error: 'Review not found' });
        }

        await logAudit(userId, 'REVIEW_UPDATE', 'reviews', id, { rating, comment }, req);

        res.json(review);
    } catch (error) {
        logger.error('Update review error:', error);
        res.status(500).json({ error: 'Failed to update review' });
    }
};

const deleteReview = async (req, res) => {
    try {
        const { id } = req.params;
        const userId = req.user.id;

        const { data: review, error } = await supabaseAdmin
            .from('reviews')
            .update({ is_deleted: true })
            .eq('id', id)
            .eq('user_id', userId)
            .select()
            .single();

        if (error || !review) {
            return res.status(404).json({ error: 'Review not found' });
        }

        await logAudit(userId, 'REVIEW_DELETE', 'reviews', id, {}, req);

        res.json({ message: 'Review deleted successfully' });
    } catch (error) {
        logger.error('Delete review error:', error);
        res.status(500).json({ error: 'Failed to delete review' });
    }
};

const getProductReviews = async (req, res) => {
    try {
        const { productId } = req.params;
        const { page = 1, limit = 10 } = req.query;
        const offset = (page - 1) * limit;

        const { data: reviews, error, count } = await supabaseAdmin
            .from('reviews')
            .select(`
                *,
                user:users(id, email)
            `, { count: 'exact' })
            .eq('product_id', productId)
            .eq('is_deleted', false)
            .range(offset, offset + limit - 1)
            .order('created_at', { ascending: false });

        if (error) {
            throw error;
        }

        res.json({
            reviews,
            pagination: {
                page: parseInt(page),
                limit: parseInt(limit),
                total: count,
                totalPages: Math.ceil(count / limit)
            }
        });
    } catch (error) {
        logger.error('Get product reviews error:', error);
        res.status(500).json({ error: 'Failed to fetch reviews' });
    }
};

const getUserReviews = async (req, res) => {
    try {
        const userId = req.user.id;
        const { page = 1, limit = 10 } = req.query;
        const offset = (page - 1) * limit;

        const { data: reviews, error, count } = await supabaseAdmin
            .from('reviews')
            .select(`
                *,
                product:products(id, title, type, image_url)
            `, { count: 'exact' })
            .eq('user_id', userId)
            .eq('is_deleted', false)
            .range(offset, offset + limit - 1)
            .order('created_at', { ascending: false });

        if (error) {
            throw error;
        }

        res.json({
            reviews,
            pagination: {
                page: parseInt(page),
                limit: parseInt(limit),
                total: count,
                totalPages: Math.ceil(count / limit)
            }
        });
    } catch (error) {
        logger.error('Get user reviews error:', error);
        res.status(500).json({ error: 'Failed to fetch reviews' });
    }
};

module.exports = {
    createReview,
    updateReview,
    deleteReview,
    getProductReviews,
    getUserReviews
};