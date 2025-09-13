<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LikeCardApiService
{
    protected $baseUrl;
    protected $deviceId;
    protected $email;
    protected $securityCode;
    protected $phone;
    protected $key;
    protected $secretKey;
    protected $secretIv;
    protected $langId;

    public function __construct()
    {
        $this->baseUrl = config('services.likecard.api_url', 'https://taxes.like4app.com/online');
        $this->deviceId = config('services.likecard.device_id');
        $this->email = config('services.likecard.email');
        $this->securityCode = config('services.likecard.security_code');
        $this->phone = config('services.likecard.phone');
        $this->key = config('services.likecard.key');
        $this->secretKey = config('services.likecard.secret_key');
        $this->secretIv = config('services.likecard.secret_iv');
        $this->langId = 1; // 1 for English
    }

    /**
     * Get the default request parameters
     */
    protected function getDefaultParams()
    {
        return [
            'deviceId' => $this->deviceId,
            'email' => $this->email,
            'securityCode' => $this->securityCode,
            'langId' => $this->langId,
        ];
    }

    /**
     * Make API request to LikeCard
     */
    protected function makeRequest($endpoint, array $params = [], $method = 'POST', $retries = 3)
    {
        $url = $this->baseUrl . '/' . $endpoint;
        $params = array_merge($this->getDefaultParams(), $params);

        $attempt = 0;
        while ($attempt < $retries) {
            try {
                Log::info('LikeCard API Request', [
                    'url' => $url,
                    'method' => $method,
                    'attempt' => $attempt + 1
                ]);

                $response = Http::asMultipart()
                    ->timeout(30)
                    ->retry(3, 100)
                    ->post($url, $this->formatMultipartData($params));

                $data = $response->json();

                Log::info('LikeCard API Response', [
                    'status' => $response->status(),
                    'response' => $data
                ]);

                if ($response->successful() && isset($data['response']) && $data['response'] == 1) {
                    return $data;
                }

                if ($response->status() === 408) {
                    // Timeout error - retry
                    $attempt++;
                    sleep(10); // Wait 10 seconds before retry
                    continue;
                }

                Log::error('LikeCard API Error', [
                    'status' => $response->status(),
                    'response' => $data
                ]);

                return null;
            } catch (\Exception $e) {
                Log::error('LikeCard API Exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                $attempt++;
                if ($attempt >= $retries) {
                    throw $e;
                }
                sleep(5);
            }
        }

        return null;
    }

    /**
     * Format data for multipart request
     */
    protected function formatMultipartData($params)
    {
        $formatted = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $formatted[] = [
                        'name' => $key . '[]',
                        'contents' => $item
                    ];
                }
            } else {
                $formatted[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }
        }
        return $formatted;
    }

    /**
     * Check API health
     */
    public function isApiHealthy()
    {
        try {
            $response = $this->makeRequest('check_balance');
            return $response !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get account balance
     */
    public function getBalance()
    {
        try {
            $response = $this->makeRequest('check_balance');

            if ($response && isset($response['response']) && $response['response'] == 1) {
                return [
                    'success' => true,
                    'balance' => $response['balance'] ?? 0,
                    'currency' => strtoupper($response['currency'] ?? 'USD'),
                    'userId' => $response['userId'] ?? null
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to retrieve balance'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get balance', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all categories and sync with database
     */
    public function syncCategories()
    {
        try {
            // Check cache first (cache for 5 hours as per API recommendation)
            $cacheKey = 'likecard_categories';
            $categories = Cache::get($cacheKey);

            if (!$categories || request()->has('force_refresh')) {
                $response = $this->makeRequest('categories');

                if (!$response || !isset($response['data'])) {
                    throw new \Exception('Failed to fetch categories');
                }

                $categories = $response['data'];
                Cache::put($cacheKey, $categories, now()->addHours(5));
            }

            // Sync categories to database
            $this->processCategoriesRecursively($categories);

            Log::info('Categories synced successfully', ['count' => count($categories)]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to sync categories', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Process categories recursively
     */
    protected function processCategoriesRecursively($categories, $parentId = null)
    {
        foreach ($categories as $categoryData) {
            // Find parent category if parentId is provided
            $parentCategory = null;
            if ($parentId) {
                $parentCategory = Category::where('api_id', $parentId)->first();
            }

            $category = Category::updateOrCreate(
                ['api_id' => $categoryData['id']],
                [
                    'parent_id' => $parentCategory ? $parentCategory->id : null,
                    'name' => $categoryData['categoryName'],
                    'image' => $categoryData['amazonImage'] ?? null,
                    'is_active' => true,
                    'slug' => Str::slug($categoryData['categoryName']),
                    'metadata' => json_encode([
                        'original_data' => $categoryData
                    ])
                ]
            );

            Log::info('Category synced', [
                'api_id' => $categoryData['id'],
                'name' => $categoryData['categoryName']
            ]);

            // Process child categories
            if (!empty($categoryData['childs'])) {
                $this->processCategoriesRecursively($categoryData['childs'], $categoryData['id']);
            }
        }
    }

    /**
     * Get products by category or product IDs
     */
    public function syncProducts($categoryId = null, $productIds = [])
    {
        try {
            // Cache key based on parameters
            $cacheKey = 'likecard_products_' . md5(json_encode(['category' => $categoryId, 'ids' => $productIds]));

            // Check cache (cache for 30 minutes as per API recommendation)
            $products = Cache::get($cacheKey);

            if (!$products || request()->has('force_refresh')) {
                $params = [];

                if ($categoryId) {
                    $params['categoryId'] = $categoryId;
                }

                if (!empty($productIds)) {
                    foreach ($productIds as $id) {
                        $params['ids'][] = $id;
                    }
                }

                $response = $this->makeRequest('products', $params);

                if (!$response || !isset($response['data'])) {
                    throw new \Exception('Failed to fetch products');
                }

                $products = $response['data'];
                Cache::put($cacheKey, $products, now()->addMinutes(30));
            }

            // Sync products to database
            foreach ($products as $productData) {
                $this->syncProductToDatabase($productData);
            }

            Log::info('Products synced successfully', ['count' => count($products)]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to sync products', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Sync all products for all categories
     */
    public function syncAllProducts()
    {
        try {
            $categories = Category::where('is_active', true)->get();

            foreach ($categories as $category) {
                if ($category->api_id) {
                    Log::info('Syncing products for category', ['category' => $category->name]);
                    $this->syncProducts($category->api_id);
                    sleep(1); // Avoid rate limiting
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to sync all products', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Sync single product to database
     */
    protected function syncProductToDatabase($productData)
    {
        try {
            // Find category by API ID
            $category = Category::where('api_id', $productData['categoryId'])->first();

            if (!$category) {
                Log::warning('Category not found for product', [
                    'category_id' => $productData['categoryId'],
                    'product' => $productData['productName']
                ]);

                // Try to sync categories first
                $this->syncCategories();
                $category = Category::where('api_id', $productData['categoryId'])->first();

                if (!$category) {
                    return;
                }
            }

            // Calculate selling price with margin
            $costPrice = floatval($productData['productPrice']);
            $sellPrice = floatval($productData['sellPrice'] ?? $productData['productPrice']);

            // Add a default margin if sell price equals cost price
            if ($sellPrice <= $costPrice) {
                $sellPrice = $costPrice * 1.1; // 10% default margin
            }

            $product = Product::updateOrCreate(
                ['api_id' => $productData['productId']],
                [
                    'category_id' => $category->id,
                    'name' => $productData['productName'],
                    'slug' => Str::slug($productData['productName']),
                    'description' => $productData['productName'], // API doesn't provide description
                    'image' => $productData['productImage'] ?? null,
                    'cost_price' => $costPrice,
                    'selling_price' => $sellPrice,
                    'currency' => $productData['productCurrency'] ?? 'USD',
                    'is_available' => $productData['available'] ?? true,
                    'is_active' => true,
                    'vat_percentage' => $productData['vatPercentage'] ?? 0,
                    'optional_fields' => json_encode($productData['productOptionalFields'] ?? []),
                    'metadata' => json_encode([
                        'optional_fields_exist' => $productData['optionalFieldsExist'] ?? 0,
                        'original_data' => $productData
                    ])
                ]
            );

            Log::info('Product synced', [
                'api_id' => $productData['productId'],
                'name' => $productData['productName'],
                'category' => $category->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to sync product to database', [
                'error' => $e->getMessage(),
                'product' => $productData
            ]);
        }
    }

    /**
     * Get all orders
     */
    public function getOrders($page = 1, $orderType = null, $fromUnixTime = null, $toUnixTime = null)
    {
        try {
            $params = ['page' => $page];

            if ($orderType) {
                $params['orderType'] = $orderType;
            }

            if ($fromUnixTime) {
                $params['fromUnixTime'] = $fromUnixTime;
            }

            if ($toUnixTime) {
                $params['toUnixTime'] = $toUnixTime;
            }

            $response = $this->makeRequest('orders', $params);

            if ($response && isset($response['data'])) {
                return [
                    'success' => true,
                    'orders' => $response['data']
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to retrieve orders'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get orders', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get order details by order ID or reference ID
     */
    public function getOrderDetails($orderId = null, $referenceId = null)
    {
        try {
            if (!$orderId && !$referenceId) {
                throw new \Exception('Either orderId or referenceId is required');
            }

            $params = [];
            if ($orderId) {
                $params['orderId'] = $orderId;
            }
            if ($referenceId) {
                $params['referenceId'] = $referenceId;
            }

            $response = $this->makeRequest('orders/details', $params);

            if ($response && isset($response['response']) && $response['response'] == 1) {
                return [
                    'success' => true,
                    'order' => $response
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to retrieve order details'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get order details', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Create order
     */
    public function createOrder($productId, $quantity = 1, $optionalFields = [], $referenceId = null)
    {
        try {
            $referenceId = $referenceId ?: 'ORDER_' . time() . '_' . uniqid();
            $time = time();
            $hash = $this->generateHash($time);

            $params = [
                'productId' => $productId,
                'quantity' => $quantity,
                'referenceId' => $referenceId,
                'time' => $time,
                'hash' => $hash
            ];

            if (!empty($optionalFields)) {
                $params['optionalFields'] = json_encode($optionalFields);
            }

            // Implement retry logic for timeout errors
            $maxRetries = 6;
            $retryDelay = 10; // seconds

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                Log::info('Creating order attempt', [
                    'attempt' => $attempt,
                    'referenceId' => $referenceId
                ]);

                $response = $this->makeRequest('create_order', $params);

                if ($response && isset($response['response']) && $response['response'] == 1) {
                    // Order created successfully
                    return [
                        'success' => true,
                        'orderId' => $response['orderId'],
                        'orderPrice' => $response['orderPrice'],
                        'orderPriceWithoutVat' => $response['orderPriceWithoutVat'],
                        'vatAmount' => $response['vatAmount'],
                        'vatPercentage' => $response['vatPercentage'],
                        'productName' => $response['productName'],
                        'productImage' => $response['productImage'],
                        'serials' => $response['serials'],
                        'referenceId' => $referenceId
                    ];
                }

                // Check if it's a timeout error
                if ($response === null || (isset($response['response']) && $response['response'] == 408)) {
                    // Try to get order details using reference ID
                    $orderDetails = $this->getOrderDetails(null, $referenceId);

                    if ($orderDetails['success']) {
                        // Order was created despite timeout
                        return [
                            'success' => true,
                            'orderId' => $orderDetails['order']['orderNumber'],
                            'order' => $orderDetails['order'],
                            'referenceId' => $referenceId
                        ];
                    }

                    if ($attempt < $maxRetries) {
                        sleep($retryDelay);
                        continue;
                    }
                }

                // Other error
                break;
            }

            return [
                'success' => false,
                'message' => 'Failed to create order after ' . $maxRetries . ' attempts'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create order', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate hash for order creation
     */
    protected function generateHash($time)
    {
        $email = strtolower($this->email);
        $phone = $this->phone;
        $key = $this->key;

        return hash('sha256', $time . $email . $phone . $key);
    }

    /**
     * Decrypt serial code
     */
    public function decryptSerial($encryptedText)
    {
        try {
            $encryptMethod = 'AES-256-CBC';
            $key = hash('sha256', $this->secretKey);
            $iv = substr(hash('sha256', $this->secretIv), 0, 16);

            return openssl_decrypt(base64_decode($encryptedText), $encryptMethod, $key, 0, $iv);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt serial', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check product availability
     */
    public function checkProductAvailability($productId)
    {
        try {
            $response = $this->syncProducts(null, [$productId]);

            if ($response) {
                $product = Product::where('api_id', $productId)->first();
                return $product && $product->is_available;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to check product availability', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Validate API credentials
     */
    public function validateCredentials()
    {
        $missing = [];

        if (!$this->deviceId) $missing[] = 'LIKECARD_DEVICE_ID';
        if (!$this->email) $missing[] = 'LIKECARD_EMAIL';
        if (!$this->securityCode) $missing[] = 'LIKECARD_SECURITY_CODE';
        if (!$this->phone) $missing[] = 'LIKECARD_PHONE';
        if (!$this->key) $missing[] = 'LIKECARD_KEY';
        if (!$this->secretKey) $missing[] = 'LIKECARD_SECRET_KEY';
        if (!$this->secretIv) $missing[] = 'LIKECARD_SECRET_IV';

        if (!empty($missing)) {
            throw new \Exception('Missing API credentials: ' . implode(', ', $missing));
        }

        return true;
    }

    /**
     * Format price with currency
     */
    public function formatPrice($price, $currency = 'USD')
    {
        $symbols = [
            'USD' => '$',
            'SAR' => 'SAR ',
            'EUR' => '€',
            'GBP' => '£'
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';
        return $symbol . number_format($price, 2);
    }

    /**
     * Get cached categories
     */
    public function getCachedCategories()
    {
        return Cache::get('likecard_categories', []);
    }

    /**
     * Clear all caches
     */
    public function clearCaches()
    {
        Cache::forget('likecard_categories');

        // Clear all product caches
        $keys = Cache::get('likecard_product_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Log::info('LikeCard API caches cleared');
    }

    /**
     * Get API statistics
     */
    public function getStatistics()
    {
        $balance = $this->getBalance();
        $categoriesCount = Category::count();
        $productsCount = Product::count();
        $ordersCount = Order::where('api_order_id', '!=', null)->count();

        return [
            'balance' => $balance['balance'] ?? 0,
            'currency' => $balance['currency'] ?? 'USD',
            'categories' => $categoriesCount,
            'products' => $productsCount,
            'orders' => $ordersCount,
            'api_healthy' => $this->isApiHealthy()
        ];
    }
}
