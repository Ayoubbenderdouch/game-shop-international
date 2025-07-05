const { supabase, supabaseAdmin } = require('../config/supabase');
const { getCountryFromIP, getClientIP } = require('../utils/geolocation');
const { logAudit } = require('../utils/auditLogger');
const logger = require('../utils/logger');

const register = async (req, res) => {
    try {
        const { email, password } = req.body;
        const country = getCountryFromIP(getClientIP(req));

        const { data: authData, error: authError } = await supabase.auth.signUp({
            email,
            password
        });

        if (authError) {
            return res.status(400).json({ error: authError.message });
        }

        const isFirstUser = await checkFirstUser();
        
        const { data: userData, error: userError } = await supabaseAdmin
            .from('users')
            .insert({
                id: authData.user.id,
                email,
                country,
                is_admin: isFirstUser || email === process.env.ADMIN_EMAIL
            })
            .select()
            .single();

        if (userError) {
            await supabaseAdmin.auth.admin.deleteUser(authData.user.id);
            return res.status(400).json({ error: 'Failed to create user profile' });
        }

        await logAudit(authData.user.id, 'USER_REGISTER', 'users', authData.user.id, { email }, req);

        res.status(201).json({
            user: userData,
            session: authData.session
        });
    } catch (error) {
        logger.error('Registration error:', error);
        res.status(500).json({ error: 'Registration failed' });
    }
};

const login = async (req, res) => {
    try {
        const { email, password } = req.body;

        const { data: authData, error: authError } = await supabase.auth.signInWithPassword({
            email,
            password
        });

        if (authError) {
            return res.status(401).json({ error: 'Invalid credentials' });
        }

        const { data: userData, error: userError } = await supabaseAdmin
            .from('users')
            .select('*')
            .eq('id', authData.user.id)
            .single();

        if (userError || !userData) {
            return res.status(401).json({ error: 'User not found' });
        }

        const country = getCountryFromIP(getClientIP(req));
        if (userData.country !== country) {
            await supabaseAdmin
                .from('users')
                .update({ country })
                .eq('id', authData.user.id);
        }

        await logAudit(authData.user.id, 'USER_LOGIN', 'users', authData.user.id, { email }, req);

        res.json({
            user: userData,
            session: authData.session
        });
    } catch (error) {
        logger.error('Login error:', error);
        res.status(500).json({ error: 'Login failed' });
    }
};

const logout = async (req, res) => {
    try {
        const token = req.headers.authorization?.replace('Bearer ', '');
        
        if (token) {
            await supabase.auth.signOut();
            await logAudit(req.user.id, 'USER_LOGOUT', 'users', req.user.id, {}, req);
        }

        res.json({ message: 'Logged out successfully' });
    } catch (error) {
        logger.error('Logout error:', error);
        res.status(500).json({ error: 'Logout failed' });
    }
};

const getProfile = async (req, res) => {
    try {
        const { data: orders } = await supabaseAdmin
            .from('orders')
            .select('id')
            .eq('user_id', req.user.id)
            .eq('status', 'completed');

        const { data: reviews } = await supabaseAdmin
            .from('reviews')
            .select('id')
            .eq('user_id', req.user.id)
            .eq('is_deleted', false);

        res.json({
            ...req.user,
            stats: {
                total_orders: orders?.length || 0,
                total_reviews: reviews?.length || 0
            }
        });
    } catch (error) {
        logger.error('Get profile error:', error);
        res.status(500).json({ error: 'Failed to get profile' });
    }
};

const checkFirstUser = async () => {
    const { count } = await supabaseAdmin
        .from('users')
        .select('*', { count: 'exact', head: true });
    
    return count === 0;
};

module.exports = {
    register,
    login,
    logout,
    getProfile
};