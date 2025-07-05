const { supabaseAdmin } = require('../config/supabase');
const logger = require('../utils/logger');

const getProducts = async (req, res) => {
    try {
        const { 
            page = 1, 
            limit = 20, 
            type, 
            category, 
            minPrice, 
            maxPrice, 
            search,
            tags,
            country,
            includePreorder = false
        } = req.query;

        const offset = (page - 1) * limit;

        let query = supabaseAdmin
            .from('products')
            .select(`
                *,
                category:categories(id, name, slug),
                reviews(rating)
            `)
            .eq('is_active', true);

        if (!includePreorder) {
            query = query.eq('is_preorder', false);
        }

        if (type) {
            query = query.eq('type', type);
        }

        if (category) {
            query = query.eq('category_id', category);
        }

        if (minPrice) {
            query = query.gte('price', minPrice);
        }

        if (maxPrice) {
            query = query.lte('price', maxPrice);
        }

        if (search) {
            query = query.or(`title.ilike.%${search}%,description.ilike.%${search}%`);
        }

        if (tags) {
            const tagArray = Array.isArray(tags) ? tags : [tags];
            query = query.contains('tags', tagArray);
        }

        if (country) {
            query = query.contains('country_availability', [country]);
        }

        const { data: products, error, count } = await query
            .range(offset, offset + limit - 1)
            .order('created_at', { ascending: false });

        if (error) {
            throw error;
        }

        const productsWithRating = products.map(product => {
            const ratings = product.reviews.map(r => r.rating);
            const avgRating = ratings.length > 0 
                ? ratings.reduce((a, b) => a + b, 0) / ratings.length 
                : 0;
            
            delete product.reviews;
            
            return {
                ...product,
                average_rating: parseFloat(avgRating.toFixed(1)),
                review_count: ratings.length
            };
        });

        res.json({
            products: productsWithRating,
            pagination: {
                page: parseInt(page),
                limit: parseInt(limit),
                total: count,
                totalPages: Math.ceil(count / limit)
            }
        });
    } catch (error) {
        logger.error('Get products error:', error);
        res.status(500).json({ error: 'Failed to fetch products' });
    }
};

const getProduct = async (req, res) => {
    try {
        const { id } = req.params;

        const { data: product, error } = await supabaseAdmin
            .from('products')
            .select(`
                *,
                category:categories(id, name, slug),
                reviews(
                    id,
                    rating,
                    comment,
                    created_at,
                    user:users(id, email)
                )
            `)
            .eq('id', id)
            .eq('is_active', true)
            .single();

        if (error || !product) {
            return res.status(404).json({ error: 'Product not found' });
        }

        const reviews = product.reviews.filter(r => !r.is_deleted);
        const ratings = reviews.map(r => r.rating);
        const avgRating = ratings.length > 0 
            ? ratings.reduce((a, b) => a + b, 0) / ratings.length 
            : 0;

        res.json({
            ...product,
            reviews,
            average_rating: parseFloat(avgRating.toFixed(1)),
            review_count: reviews.length
        });
    } catch (error) {
        logger.error('Get product error:', error);
        res.status(500).json({ error: 'Failed to fetch product' });
    }
};

const getCategories = async (req, res) => {
    try {
        const { data: categories, error } = await supabaseAdmin
            .from('categories')
            .select('*')
            .order('name');

        if (error) {
            throw error;
        }

        res.json(categories);
    } catch (error) {
        logger.error('Get categories error:', error);
        res.status(500).json({ error: 'Failed to fetch categories' });
    }
};

const getProductsByCategory = async (req, res) => {
    try {
        const { slug } = req.params;

        const { data: category, error: categoryError } = await supabaseAdmin
            .from('categories')
            .select('id')
            .eq('slug', slug)
            .single();

        if (categoryError || !category) {
            return res.status(404).json({ error: 'Category not found' });
        }

        req.query.category = category.id;
        return getProducts(req, res);
    } catch (error) {
        logger.error('Get products by category error:', error);
        res.status(500).json({ error: 'Failed to fetch products' });
    }
};

module.exports = {
    getProducts,
    getProduct,
    getCategories,
    getProductsByCategory
};