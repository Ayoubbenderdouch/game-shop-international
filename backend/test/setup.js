const { supabaseAdmin } = require('../config/supabase');

const testUserEmail = 'test@example.com';
const testAdminEmail = 'admin@example.com';
const testPassword = 'testpassword123';

const setupTestData = async () => {
    try {
        const { data: { user: testUser }, error: testUserError } = await supabaseAdmin.auth.admin.createUser({
            email: testUserEmail,
            password: testPassword,
            email_confirm: true
        });

        if (testUser && !testUserError) {
            await supabaseAdmin.from('users').insert({
                id: testUser.id,
                email: testUserEmail,
                country: 'US',
                is_admin: false
            });
        }

        const { data: { user: adminUser }, error: adminUserError } = await supabaseAdmin.auth.admin.createUser({
            email: testAdminEmail,
            password: testPassword,
            email_confirm: true
        });

        if (adminUser && !adminUserError) {
            await supabaseAdmin.from('users').insert({
                id: adminUser.id,
                email: testAdminEmail,
                country: 'US',
                is_admin: true
            });
        }

        const { data: category } = await supabaseAdmin
            .from('categories')
            .select('id')
            .eq('slug', 'game-cards')
            .single();

        if (category) {
            const { data: product } = await supabaseAdmin
                .from('products')
                .insert({
                    title: 'Test Steam Card',
                    type: 'game_card',
                    price: 50.00,
                    description: 'Test product',
                    category_id: category.id,
                    tags: ['test'],
                    country_availability: ['US'],
                    is_active: true
                })
                .select()
                .single();

            if (product) {
                await supabaseAdmin
                    .from('product_codes')
                    .insert([
                        { product_id: product.id, code: 'TEST-CODE-001' },
                        { product_id: product.id, code: 'TEST-CODE-002' },
                        { product_id: product.id, code: 'TEST-CODE-003' }
                    ]);
            }
        }

        console.log('Test data setup complete');
    } catch (error) {
        console.error('Setup error:', error);
    }
};

const cleanupTestData = async () => {
    try {
        const { data: testUser } = await supabaseAdmin
            .from('users')
            .select('id')
            .eq('email', testUserEmail)
            .single();

        const { data: adminUser } = await supabaseAdmin
            .from('users')
            .select('id')
            .eq('email', testAdminEmail)
            .single();

        if (testUser) {
            await supabaseAdmin.auth.admin.deleteUser(testUser.id);
        }

        if (adminUser) {
            await supabaseAdmin.auth.admin.deleteUser(adminUser.id);
        }

        await supabaseAdmin
            .from('products')
            .delete()
            .eq('title', 'Test Steam Card');

        console.log('Test data cleanup complete');
    } catch (error) {
        console.error('Cleanup error:', error);
    }
};

module.exports = { setupTestData, cleanupTestData };