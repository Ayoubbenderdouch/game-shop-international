const stripe = require('../config/stripe');
const { supabaseAdmin } = require('../config/supabase');
const { decrypt } = require('../utils/encryption');
const { logAudit } = require('../utils/auditLogger');
const logger = require('../utils/logger');

const handleWebhook = async (req, res) => {
    const sig = req.headers['stripe-signature'];
    const endpointSecret = process.env.STRIPE_WEBHOOK_SECRET;

    let event;

    try {
        event = stripe.webhooks.constructEvent(req.body, sig, endpointSecret);
    } catch (err) {
        logger.error('Webhook signature verification failed:', err);
        return res.status(400).send(`Webhook Error: ${err.message}`);
    }

    try {
        switch (event.type) {
            case 'checkout.session.completed':
                await handleCheckoutSessionCompleted(event.data.object);
                break;
            
            case 'payment_intent.payment_failed':
                await handlePaymentFailed(event.data.object);
                break;

            case 'charge.refunded':
                await handleRefund(event.data.object);
                break;

            default:
                logger.info(`Unhandled event type ${event.type}`);
        }

        res.json({ received: true });
    } catch (error) {
        logger.error('Webhook processing error:', error);
        res.status(500).json({ error: 'Webhook processing failed' });
    }
};

const handleCheckoutSessionCompleted = async (session) => {
    const orderId = session.metadata.order_id;
    const userId = session.metadata.user_id;
    const items = JSON.parse(session.metadata.items);

    const { data: order, error: orderError } = await supabaseAdmin
        .from('orders')
        .update({
            status: 'processing',
            stripe_payment_intent_id: session.payment_intent
        })
        .eq('id', orderId)
        .select()
        .single();

    if (orderError) {
        throw new Error(`Failed to update order: ${orderError.message}`);
    }

    const allocatedCodes = [];
    
    for (const item of items) {
        for (let i = 0; i < item.quantity; i++) {
            const { data: availableCode, error: codeError } = await supabaseAdmin
                .from('product_codes')
                .select('id, code')
                .eq('product_id', item.productId)
                .eq('is_used', false)
                .limit(1)
                .single();

            if (codeError || !availableCode) {
                logger.error(`No available codes for product ${item.productId}`);
                continue;
            }

            const { error: updateError } = await supabaseAdmin
                .from('product_codes')
                .update({
                    is_used: true,
                    used_by: userId,
                    used_at: new Date().toISOString()
                })
                .eq('id', availableCode.id);

            if (updateError) {
                logger.error(`Failed to mark code as used: ${updateError.message}`);
                continue;
            }

            const { data: orderItem, error: itemError } = await supabaseAdmin
                .from('order_items')
                .insert({
                    order_id: orderId,
                    product_id: item.productId,
                    product_code_id: availableCode.id,
                    quantity: 1,
                    price: item.price || 0
                })
                .select()
                .single();

            if (!itemError) {
                allocatedCodes.push({
                    productId: item.productId,
                    code: decrypt(availableCode.code)
                });
            }
        }
    }

    await supabaseAdmin
        .from('orders')
        .update({ status: 'completed' })
        .eq('id', orderId);

    await logAudit(userId, 'ORDER_COMPLETED', 'orders', orderId, { 
        payment_intent: session.payment_intent,
        allocated_codes: allocatedCodes.length 
    }, { headers: {} });

    logger.info(`Order ${orderId} completed with ${allocatedCodes.length} codes allocated`);
};

const handlePaymentFailed = async (paymentIntent) => {
    const { data: order } = await supabaseAdmin
        .from('orders')
        .select('id')
        .eq('stripe_payment_intent_id', paymentIntent.id)
        .single();

    if (order) {
        await supabaseAdmin
            .from('orders')
            .update({ status: 'failed' })
            .eq('id', order.id);

        logger.info(`Order ${order.id} marked as failed`);
    }
};

const handleRefund = async (charge) => {
    const { data: order } = await supabaseAdmin
        .from('orders')
        .select('id, order_items(product_code_id)')
        .eq('stripe_payment_intent_id', charge.payment_intent)
        .single();

    if (order) {
        await supabaseAdmin
            .from('orders')
            .update({ status: 'refunded' })
            .eq('id', order.id);

        for (const item of order.order_items) {
            if (item.product_code_id) {
                await supabaseAdmin
                    .from('product_codes')
                    .update({
                        is_used: false,
                        used_by: null,
                        used_at: null
                    })
                    .eq('id', item.product_code_id);
            }
        }

        logger.info(`Order ${order.id} refunded and codes released`);
    }
};

const getCheckoutSession = async (req, res) => {
    try {
        const { sessionId } = req.params;

        const session = await stripe.checkout.sessions.retrieve(sessionId);

        if (!session) {
            return res.status(404).json({ error: 'Session not found' });
        }

        const { data: order } = await supabaseAdmin
            .from('orders')
            .select('*')
            .eq('stripe_session_id', sessionId)
            .single();

        res.json({
            session: {
                id: session.id,
                payment_status: session.payment_status,
                status: session.status
            },
            order
        });
    } catch (error) {
        logger.error('Get checkout session error:', error);
        res.status(500).json({ error: 'Failed to retrieve session' });
    }
};

module.exports = {
    handleWebhook,
    getCheckoutSession
};