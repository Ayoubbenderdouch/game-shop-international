const { supabaseAdmin } = require('../config/supabase');
const stripe = require('../config/stripe');
const { getCountryFromIP, getClientIP } = require('../utils/geolocation');
const { logAudit } = require('../utils/auditLogger');
const logger = require('../utils/logger');

const createCheckoutSession = async (req, res) => {
    try {
        const { items } = req.body;
        const userId = req.user.id;

        if (!items || items.length === 0) {
            return res.status(400).json({ error: 'No items provided' });
        }

        const productIds = items.map(item => item.productId);
        
        const { data: products, error: productsError } = await supabaseAdmin
            .from('products')
            .select('*')
            .in('id', productIds)
            .eq('is_active', true);

        if (productsError || !products) {
            return res.status(400).json({ error: 'Failed to fetch products' });
        }

        const productMap = new Map(products.map(p => [p.id, p]));
        let hasInsufficientStock = false;
        
        for (const item of items) {
            const product = productMap.get(item.productId);
            
            if (!product) {
                return res.status(400).json({ error: `Product ${item.productId} not found` });
            }

            if (product.is_preorder) {
                return res.status(400).json({ error: `Product ${product.title} is a pre-order and cannot be purchased yet` });
            }

            if (product.stock_count < item.quantity) {
                hasInsufficientStock = true;
                return res.status(400).json({ 
                    error: `Insufficient stock for ${product.title}. Available: ${product.stock_count}` 
                });
            }
        }

        const lineItems = items.map(item => {
            const product = productMap.get(item.productId);
            return {
                price_data: {
                    currency: 'usd',
                    product_data: {
                        name: product.title,
                        description: product.description,
                        images: product.image_url ? [product.image_url] : []
                    },
                    unit_amount: Math.round(product.price * 100)
                },
                quantity: item.quantity
            };
        });

        const country = getCountryFromIP(getClientIP(req));

        const { data: order, error: orderError } = await supabaseAdmin
            .from('orders')
            .insert({
                user_id: userId,
                total_amount: items.reduce((sum, item) => {
                    const product = productMap.get(item.productId);
                    return sum + (product.price * item.quantity);
                }, 0),
                status: 'pending',
                country
            })
            .select()
            .single();

        if (orderError) {
            return res.status(500).json({ error: 'Failed to create order' });
        }

        const session = await stripe.checkout.sessions.create({
            payment_method_types: ['card'],
            line_items: lineItems,
            mode: 'payment',
            success_url: `${process.env.FRONTEND_URL}/order-success?session_id={CHECKOUT_SESSION_ID}`,
            cancel_url: `${process.env.FRONTEND_URL}/cart`,
            customer_email: req.user.email,
            metadata: {
                order_id: order.id,
                user_id: userId,
                items: JSON.stringify(items)
            }
        });

        await supabaseAdmin
            .from('orders')
            .update({ stripe_session_id: session.id })
            .eq('id', order.id);

        await logAudit(userId, 'ORDER_CREATE', 'orders', order.id, { items, total: order.total_amount }, req);

        res.json({ 
            sessionId: session.id,
            sessionUrl: session.url,
            orderId: order.id
        });
    } catch (error) {
        logger.error('Create checkout session error:', error);
        res.status(500).json({ error: 'Failed to create checkout session' });
    }
};

const getUserOrders = async (req, res) => {
    try {
        const { page = 1, limit = 10, status } = req.query;
        const offset = (page - 1) * limit;

        let query = supabaseAdmin
            .from('orders')
            .select(`
                *,
                order_items(
                    *,
                    product:products(id, title, type, image_url)
                )
            `, { count: 'exact' })
            .eq('user_id', req.user.id);

        if (status) {
            query = query.eq('status', status);
        }

        const { data: orders, error, count } = await query
            .range(offset, offset + limit - 1)
            .order('created_at', { ascending: false });

        if (error) {
            throw error;
        }

        res.json({
            orders,
            pagination: {
                page: parseInt(page),
                limit: parseInt(limit),
                total: count,
                totalPages: Math.ceil(count / limit)
            }
        });
    } catch (error) {
        logger.error('Get user orders error:', error);
        res.status(500).json({ error: 'Failed to fetch orders' });
    }
};

const getOrder = async (req, res) => {
    try {
        const { id } = req.params;

        const { data: order, error } = await supabaseAdmin
            .from('orders')
            .select(`
                *,
                order_items(
                    *,
                    product:products(id, title, type, image_url),
                    product_code:product_codes(code)
                )
            `)
            .eq('id', id)
            .eq('user_id', req.user.id)
            .single();

        if (error || !order) {
            return res.status(404).json({ error: 'Order not found' });
        }

        const orderWithCodes = {
            ...order,
            order_items: order.order_items.map(item => ({
                ...item,
                code: item.product_code?.code || null,
                product_code: undefined
            }))
        };

        res.json(orderWithCodes);
    } catch (error) {
        logger.error('Get order error:', error);
        res.status(500).json({ error: 'Failed to fetch order' });
    }
};

const resendOrderCodes = async (req, res) => {
    try {
        const { id } = req.params;

        const { data: order, error } = await supabaseAdmin
            .from('orders')
            .select(`
                *,
                order_items(
                    *,
                    product:products(title),
                    product_code:product_codes(code)
                )
            `)
            .eq('id', id)
            .eq('user_id', req.user.id)
            .eq('status', 'completed')
            .single();

        if (error || !order) {
            return res.status(404).json({ error: 'Order not found or not completed' });
        }

        await logAudit(req.user.id, 'ORDER_RESEND_CODES', 'orders', id, {}, req);

        res.json({ message: 'Order codes have been resent to your email' });
    } catch (error) {
        logger.error('Resend order codes error:', error);
        res.status(500).json({ error: 'Failed to resend codes' });
    }
};
// Add this function to the existing order.controller.js

const createMockCheckout = async (req, res) => {
    const transaction = await db.transaction();
    
    try {
        const { items } = req.body;
        const userId = req.user.id;
        
        // Validate items
        if (!items || !Array.isArray(items) || items.length === 0) {
            return res.status(400).json({ error: 'Invalid items' });
        }
        
        // Calculate total and validate stock
        let totalAmount = 0;
        const orderItems = [];
        
        for (const item of items) {
            const product = await db.Product.findByPk(item.productId);
            if (!product || !product.is_active) {
                await transaction.rollback();
                return res.status(400).json({ error: `Product ${item.productId} not available` });
            }
            
            const availableStock = await db.Stock.count({
                where: { 
                    product_id: item.productId,
                    is_used: false
                }
            });
            
            if (availableStock < item.quantity) {
                await transaction.rollback();
                return res.status(400).json({ error: `Insufficient stock for ${product.title}` });
            }
            
            totalAmount += product.price * item.quantity;
            orderItems.push({
                product,
                quantity: item.quantity,
                price: product.price
            });
        }
        
        // Create order
        const order = await db.Order.create({
            id: uuidv4(),
            user_id: userId,
            total_amount: totalAmount,
            status: 'completed',
            payment_method: 'mock',
            payment_status: 'paid'
        }, { transaction });
        
        // Create order items and assign codes
        for (const item of orderItems) {
            const orderItem = await db.OrderItem.create({
                id: uuidv4(),
                order_id: order.id,
                product_id: item.product.id,
                quantity: item.quantity,
                price: item.price
            }, { transaction });
            
            // Assign codes
            const codes = await db.Stock.findAll({
                where: {
                    product_id: item.product.id,
                    is_used: false
                },
                limit: item.quantity,
                transaction
            });
            
            for (const code of codes) {
                await code.update({
                    is_used: true,
                    used_by: userId,
                    used_at: new Date(),
                    order_item_id: orderItem.id
                }, { transaction });
            }
        }
        
        await transaction.commit();
        
        // Send order confirmation email (mock)
        console.log('Mock email sent for order:', order.id);
        
        res.json({
            success: true,
            orderId: order.id,
            message: 'Order completed successfully'
        });
        
    } catch (error) {
        await transaction.rollback();
        logger.error('Mock checkout error:', error);
        res.status(500).json({ error: 'Checkout failed' });
    }
};

module.exports = {
    createCheckoutSession,
    getUserOrders,
    getOrder,
    resendOrderCodes,
    createMockCheckout
};