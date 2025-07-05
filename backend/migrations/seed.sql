-- Seed Data for Game & Gift Card Shop

-- Insert Categories
INSERT INTO categories (name, slug) VALUES
('Game Cards', 'game-cards'),
('Gift Cards', 'gift-cards'),
('Subscriptions', 'subscriptions'),
('Game Top-Ups', 'game-topups');

-- Insert Sample Products
INSERT INTO products (title, type, price, description, image_url, category_id, tags, country_availability, is_active, stock_count) VALUES
-- Game Cards
('Steam Gift Card $20', 'game_card', 20.00, 'Add $20 to your Steam Wallet', 'https://example.com/steam-20.jpg', 
 (SELECT id FROM categories WHERE slug = 'game-cards'), 
 ARRAY['steam', 'gaming', 'pc'], 
 ARRAY['US', 'CA', 'UK', 'EU'], 
 true, 0),

('Steam Gift Card $50', 'game_card', 50.00, 'Add $50 to your Steam Wallet', 'https://example.com/steam-50.jpg', 
 (SELECT id FROM categories WHERE slug = 'game-cards'), 
 ARRAY['steam', 'gaming', 'pc'], 
 ARRAY['US', 'CA', 'UK', 'EU'], 
 true, 0),

('PlayStation Store $25', 'game_card', 25.00, 'PlayStation Store gift card worth $25', 'https://example.com/psn-25.jpg', 
 (SELECT id FROM categories WHERE slug = 'game-cards'), 
 ARRAY['playstation', 'ps5', 'ps4', 'gaming'], 
 ARRAY['US', 'CA'], 
 true, 0),

('Xbox Gift Card $50', 'game_card', 50.00, 'Xbox gift card for games and entertainment', 'https://example.com/xbox-50.jpg', 
 (SELECT id FROM categories WHERE slug = 'game-cards'), 
 ARRAY['xbox', 'microsoft', 'gaming'], 
 ARRAY['US', 'CA', 'UK'], 
 true, 0),

-- Gift Cards
('Amazon Gift Card $25', 'gift_card', 25.00, 'Amazon.com gift card', 'https://example.com/amazon-25.jpg', 
 (SELECT id FROM categories WHERE slug = 'gift-cards'), 
 ARRAY['amazon', 'shopping'], 
 ARRAY['US'], 
 true, 0),

('Google Play Gift Card $15', 'gift_card', 15.00, 'Google Play Store credit', 'https://example.com/google-15.jpg', 
 (SELECT id FROM categories WHERE slug = 'gift-cards'), 
 ARRAY['google', 'android', 'apps'], 
 ARRAY['US', 'CA', 'UK', 'EU'], 
 true, 0),

('Apple Gift Card $50', 'gift_card', 50.00, 'Apple Store and iTunes gift card', 'https://example.com/apple-50.jpg', 
 (SELECT id FROM categories WHERE slug = 'gift-cards'), 
 ARRAY['apple', 'itunes', 'ios'], 
 ARRAY['US', 'CA', 'UK'], 
 true, 0),

-- Subscriptions
('Netflix Gift Card 1 Month', 'subscription', 15.99, 'Netflix subscription for 1 month', 'https://example.com/netflix-1m.jpg', 
 (SELECT id FROM categories WHERE slug = 'subscriptions'), 
 ARRAY['netflix', 'streaming', 'entertainment'], 
 ARRAY['US', 'CA', 'UK', 'EU'], 
 true, 0),

('YouTube Premium 3 Months', 'subscription', 35.97, 'YouTube Premium subscription for 3 months', 'https://example.com/youtube-3m.jpg', 
 (SELECT id FROM categories WHERE slug = 'subscriptions'), 
 ARRAY['youtube', 'streaming', 'google'], 
 ARRAY['US', 'CA', 'UK'], 
 true, 0),

('Spotify Premium 6 Months', 'subscription', 59.94, 'Spotify Premium subscription for 6 months', 'https://example.com/spotify-6m.jpg', 
 (SELECT id FROM categories WHERE slug = 'subscriptions'), 
 ARRAY['spotify', 'music', 'streaming'], 
 ARRAY['US', 'CA', 'UK', 'EU'], 
 true, 0),

-- UC Top-Ups
('PUBG Mobile 600 UC', 'uc_topup', 9.99, 'PUBG Mobile 600 UC via Razer Gold', 'https://example.com/pubg-600uc.jpg', 
 (SELECT id FROM categories WHERE slug = 'game-topups'), 
 ARRAY['pubg', 'mobile', 'uc', 'razer'], 
 ARRAY['US', 'CA', 'UK', 'EU', 'ASIA'], 
 true, 0),

('PUBG Mobile 1800 UC', 'uc_topup', 24.99, 'PUBG Mobile 1800 UC via Razer Gold', 'https://example.com/pubg-1800uc.jpg', 
 (SELECT id FROM categories WHERE slug = 'game-topups'), 
 ARRAY['pubg', 'mobile', 'uc', 'razer'], 
 ARRAY['US', 'CA', 'UK', 'EU', 'ASIA'], 
 true, 0),

('Free Fire 2200 Diamonds', 'uc_topup', 19.99, 'Free Fire 2200 Diamonds top-up', 'https://example.com/freefire-2200.jpg', 
 (SELECT id FROM categories WHERE slug = 'game-topups'), 
 ARRAY['freefire', 'mobile', 'diamonds'], 
 ARRAY['US', 'CA', 'UK', 'EU', 'ASIA'], 
 true, 0);

-- Create sample stock alerts for products with low stock
INSERT INTO stock_alerts (product_id, threshold, is_active)
SELECT id, 10, true FROM products;