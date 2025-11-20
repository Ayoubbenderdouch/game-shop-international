<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MazayaService
{
    private $baseUrl;
    private $username;
    private $password;
    private $token;

    // Supported games - only these 6 games
    private $supportedGames = [
        'PUBG Mobile',
        'Free Fire',
        'FC Mobile',
        'Yalla Ludo',
        'Genshin Impact',
        'Mobile Legends'
    ];

    public function __construct()
    {
        $this->baseUrl = config('services.mazaya.base_url', 'https://store.mazaya-online.com/api/v1');
        $this->username = config('services.mazaya.username');
        $this->password = config('services.mazaya.password');
    }

    /**
     * Login to Mazaya API and get token
     */
    public function login()
    {
        try {
            $response = Http::asForm()->post("{$this->baseUrl}/login", [
                'username' => $this->username,
                'password' => $this->password,
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                $data = $response->json();
                $this->token = $data['token'];

                // Cache token for 1 hour
                Cache::put('mazaya_token', $this->token, 3600);
                Cache::put('mazaya_user_balance', $data['user']['balance'] ?? 0, 300);

                return [
                    'success' => true,
                    'token' => $this->token,
                    'balance' => $data['user']['balance'] ?? 0,
                    'user' => $data['user']
                ];
            }

            return ['success' => false, 'message' => 'Login failed'];
        } catch (\Exception $e) {
            Log::error('Mazaya login error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get token from cache or login
     */
    private function getToken()
    {
        if ($this->token) {
            return $this->token;
        }

        $token = Cache::get('mazaya_token');
        if ($token) {
            $this->token = $token;
            return $token;
        }

        // Token not found, login
        $result = $this->login();
        return $result['success'] ? $result['token'] : null;
    }

    /**
     * Get all categories
     */
    public function getCategories()
    {
        try {
            $response = Http::get("{$this->baseUrl}/categories");

            if ($response->successful() && $response->json('status') === 'success') {
                $categories = $response->json('categories', []);

                // Filter only gaming category (usually "شحن الألعاب")
                return array_filter($categories, function($cat) {
                    return stripos($cat['name'], 'ألعاب') !== false ||
                           stripos($cat['name'], 'game') !== false;
                });
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Mazaya getCategories error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get products by category - filtered by supported games
     */
    public function getProductsByCategory($categoryId)
    {
        try {
            $response = Http::get("{$this->baseUrl}/products", [
                'category_id' => $categoryId
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                $products = $response->json('products', []);

                // Return all products (no filtering for now to see what we have)
                return $products;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Mazaya getProductsByCategory error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get product by ID
     */
    public function getProductById($productId)
    {
        try {
            $response = Http::get("{$this->baseUrl}/products/{$productId}");

            if ($response->successful() && $response->json('status') === 'success') {
                return $response->json('product');
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Mazaya getProductById error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new order
     */
    public function createOrder($productId, $playerId, $quantity = 1, $playerName = null, $uuid = null)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Authentication failed'];
        }

        try {
            $response = Http::withToken($token)
                ->asForm()
                ->post("{$this->baseUrl}/bills", [
                    'product_id' => $productId,
                    'player_id' => $playerId,
                    'qty' => $quantity,
                    'player_name' => $playerName,
                    'uuid' => $uuid,
                ]);

            if ($response->successful() && $response->json('status') === 'success') {
                return [
                    'success' => true,
                    'order' => $response->json('order'),
                    'order_id' => $response->json('order.id')
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message', 'Order creation failed')
            ];
        } catch (\Exception $e) {
            Log::error('Mazaya createOrder error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get order status
     */
    public function getOrderStatus($orderId)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Authentication failed'];
        }

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/bills/{$orderId}");

            if ($response->successful() && $response->json('status') === 'success') {
                return [
                    'success' => true,
                    'order' => $response->json('order')
                ];
            }

            return ['success' => false, 'message' => 'Order not found'];
        } catch (\Exception $e) {
            Log::error('Mazaya getOrderStatus error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get all orders
     */
    public function getOrders($status = null)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Authentication failed'];
        }

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/bills", [
                'status' => $status
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                return [
                    'success' => true,
                    'orders' => $response->json('orders', [])
                ];
            }

            return ['success' => false, 'orders' => []];
        } catch (\Exception $e) {
            Log::error('Mazaya getOrders error: ' . $e->getMessage());
            return ['success' => false, 'orders' => []];
        }
    }

    /**
     * Get balance
     */
    public function getBalance()
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Authentication failed'];
        }

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/balances");

            if ($response->successful() && $response->json('status') === 'success') {
                $balance = $response->json('user.balance', 0);
                Cache::put('mazaya_user_balance', $balance, 300);

                return [
                    'success' => true,
                    'balance' => $balance,
                    'user' => $response->json('user')
                ];
            }

            return ['success' => false, 'balance' => 0];
        } catch (\Exception $e) {
            Log::error('Mazaya getBalance error: ' . $e->getMessage());
            return ['success' => false, 'balance' => 0];
        }
    }

    /**
     * Get keyword for filtering products by game name
     */
    private function getGameKeyword($gameName)
    {
        $keywords = [
            'PUBG Mobile' => 'pubg',
            'Free Fire' => 'free fire',
            'FC Mobile' => 'fc mobile',
            'Yalla Ludo' => 'yalla ludo',
            'Genshin Impact' => 'genshin',
            'Mobile Legends' => 'mobile legend'
        ];

        return $keywords[$gameName] ?? strtolower($gameName);
    }

    /**
     * Get supported games list
     */
    public function getSupportedGames()
    {
        return $this->supportedGames;
    }
}
