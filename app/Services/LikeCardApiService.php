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
                    'endpoint' => $endpoint,
                    'attempt' => $attempt + 1,
                    'params' => array_diff_key($params, array_flip(['securityCode', 'deviceId']))
                ]);

                $response = Http::timeout(30)
                    ->asMultipart()
                    ->post($url, $this->convertToMultipart($params));

                $responseData = $response->json();

                Log::info('LikeCard API Response', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'response' => $responseData
                ]);

                if ($response->successful() && isset($responseData['response']) && $responseData['response'] == 1) {
                    return $responseData;
                }

                // Check for specific error codes
                if (isset($responseData['response']) && $responseData['response'] == 1020) {
                    Log::error('LikeCard API: Blocked IP - Contact account manager');
                    throw new \Exception('API Access Blocked: Please contact your account manager');
                }

                if ($response->status() == 408) {
                    Log::warning('LikeCard API: Request timeout, retrying...');
                    $attempt++;
                    sleep(2); // Wait before retry
                    continue;
                }

                throw new \Exception('API request failed: ' . json_encode($responseData));

            } catch (\Exception $e) {
                Log::error('LikeCard API Error', [
                    'endpoint' => $endpoint,
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage()
                ]);

                $attempt++;
                if ($attempt >= $retries) {
                    throw $e;
                }
                sleep(2); // Wait before retry
            }
        }

        return null;
    }

    /**
     * Convert parameters to multipart format
     */
    protected function convertToMultipart($params)
    {
        $multipart = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $multipart[] = [
                        'name' => $key . '[]',
                        'contents' => $item
                    ];
                }
            } else {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }
        }
        return $multipart;
    }

    /**
     * Generate hash for order creation
     */
    protected function generateHash($timestamp)
    {
        $email = strtolower($this->email);
        return hash('sha256', $timestamp . $email . $this->phone . $this->key);
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
            Log::error('Serial decryption failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check account balance
     */
    public function getBalance()
    {
        try {
            $response = $this->makeRequest('check_balance');

            if ($response && isset($response['balance'])) {
                return [
                    'success' => true,
                    'balance' => $response['balance'],
                    'currency' => $response['currency'] ?? 'USD',
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

            if (!$categories) {
                $response = $this->makeRequest('categories');

                if (!$response || !isset($response['data'])) {
                    throw new \Exception('Failed to fetch categories');
                }

                $categories = $response['data'];
                Cache::put($cacheKey, $categories, now()->addHours(5));
            }

            // Sync categories to database
            $this->processCategoriesRecursively($categories);

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
            $category = Category::updateOrCreate(
                ['api_id' => $categoryData['id']],
                [
                    'parent_id' => $categoryData['categoryParentId'] ?? $parentId,
                    'name' => $categoryData['categoryName'],
                    'image' => $categoryData['amazonImage'] ?? null,
                    'is_active' => true,
                    'metadata' => [
                        'original_data' => $categoryData
                    ]
                ]
            );

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

            if (!$products) {
                $params = [];

                if ($categoryId) {
                    $params['categoryId'] = $categoryId;
                }

                if (!empty($productIds)) {
                    $params['ids'] = $productIds;
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

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to sync products', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Sync single product to database
     */
    protected function syncProductToDatabase($productData)
    {
        // Find category by API ID
        $category = Category::where('api_id', $productData['categoryId'])->first();

        if (!$category) {
            Log::warning('Category not found for product', ['category_id' => $productData['categoryId']]);
            return;
        }

        $product = Product::updateOrCreate(
            ['api_id' => $productData['productId']],
            [
                'category_id' => $category->id,
                'name' => $productData['productName'],
                'description' => $productData['productName'], // API doesn't provide description
                'image' => $productData['productImage'] ?? null,
                'cost_price' => $productData['productPrice'],
                'selling_price' => $productData['sellPrice'] ?? $productData['productPrice'],
                'currency' => $productData['productCurrency'] ?? 'USD',
                'is_available' => $productData['available'] ?? true,
                'is_active' => true,
                'vat_percentage' => $productData['vatPercentage'] ?? 0,
                'optional_fields' => $productData['productOptionalFields'] ?? [],
                'metadata' => [
                    'optional_fields_exist' => $productData['optionalFieldsExist'] ?? 0,
                    'original_data' => $productData
                ]
            ]
        );

        // Calculate margin
        $product->calculateSellingPrice();
        $product->save();
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

            if ($response && isset($response['orderNumber'])) {
                // Decrypt serial codes if present
                if (isset($response['serials']) && is_array($response['serials'])) {
                    foreach ($response['serials'] as &$serial) {
                        if (isset($serial['serialCode'])) {
                            $serial['decryptedCode'] = $this->decryptSerial($serial['serialCode']);
                        }
                    }
                }

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
     * Create an order
     */
    public function createOrder($productId, $referenceId, $quantity = 1, $optionalFields = [])
    {
        try {
            $timestamp = time();
            $hash = $this->generateHash($timestamp);

            $params = [
                'productId' => $productId,
                'referenceId' => $referenceId,
                'time' => $timestamp,
                'hash' => $hash,
                'quantity' => $quantity
            ];

            if (!empty($optionalFields)) {
                $params['optionalFields'] = json_encode($optionalFields);
            }

            // Implement retry logic for timeout handling
            $maxRetries = 6;
            $retryDelay = 10; // seconds
            $attempt = 0;

            while ($attempt < $maxRetries) {
                try {
                    $response = $this->makeRequest('create_order', $params, 'POST', 1);

                    if ($response && isset($response['orderId'])) {
                        // Decrypt serial codes if present
                        if (isset($response['serials']) && is_array($response['serials'])) {
                            foreach ($response['serials'] as &$serial) {
                                if (isset($serial['serialCode'])) {
                                    $serial['decryptedCode'] = $this->decryptSerial($serial['serialCode']);
                                }
                            }
                        }

                        return [
                            'success' => true,
                            'orderId' => $response['orderId'],
                            'orderPrice' => $response['orderPrice'] ?? null,
                            'orderPriceWithoutVat' => $response['orderPriceWithoutVat'] ?? null,
                            'vatAmount' => $response['vatAmount'] ?? null,
                            'vatPercentage' => $response['vatPercentage'] ?? null,
                            'productName' => $response['productName'] ?? null,
                            'productImage' => $response['productImage'] ?? null,
                            'serials' => $response['serials'] ?? []
                        ];
                    }

                    // If response doesn't have orderId, check using referenceId
                    $orderDetails = $this->getOrderDetails(null, $referenceId);
                    if ($orderDetails['success']) {
                        return $orderDetails;
                    }

                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'timeout') !== false || strpos($e->getMessage(), '408') !== false) {
                        $attempt++;
                        Log::warning('Create order timeout, retrying...', [
                            'attempt' => $attempt,
                            'referenceId' => $referenceId
                        ]);

                        if ($attempt < $maxRetries) {
                            sleep($retryDelay);

                            // Try to get order details with referenceId
                            $orderDetails = $this->getOrderDetails(null, $referenceId);
                            if ($orderDetails['success']) {
                                return $orderDetails;
                            }
                            continue;
                        }
                    }
                    throw $e;
                }
            }

            // If all retries failed, implement health check
            $this->performHealthCheck($referenceId);

            return [
                'success' => false,
                'message' => 'Failed to create order after multiple attempts'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create order', [
                'error' => $e->getMessage(),
                'productId' => $productId,
                'referenceId' => $referenceId
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Perform health check when order creation fails
     */
    protected function performHealthCheck($referenceId)
    {
        Log::warning('Performing health check for order', ['referenceId' => $referenceId]);

        // Set a flag to pause new orders
        Cache::put('likecard_api_health_check', true, now()->addMinutes(10));

        // Schedule a job to check order status every 60 seconds
        dispatch(function () use ($referenceId) {
            $maxAttempts = 10;
            $attempt = 0;

            while ($attempt < $maxAttempts) {
                sleep(60);

                $orderDetails = $this->getOrderDetails(null, $referenceId);
                if ($orderDetails['success']) {
                    // API is responsive, remove health check flag
                    Cache::forget('likecard_api_health_check');
                    Log::info('Health check passed, API is responsive');
                    break;
                }

                $attempt++;
            }
        })->afterResponse();
    }

    /**
     * Check if API is healthy
     */
    public function isApiHealthy()
    {
        return !Cache::has('likecard_api_health_check');
    }

    /**
     * Check product availability
     */
    public function checkProductAvailability($productId)
    {
        try {
            $params = ['ids' => [$productId]];
            $response = $this->makeRequest('products', $params);

            if ($response && isset($response['data']) && count($response['data']) > 0) {
                $product = $response['data'][0];
                return [
                    'success' => true,
                    'available' => $product['available'] ?? false,
                    'price' => $product['productPrice'] ?? null,
                    'sellPrice' => $product['sellPrice'] ?? null,
                    'vatPercentage' => $product['vatPercentage'] ?? 0
                ];
            }

            return [
                'success' => false,
                'available' => false,
                'message' => 'Product not found'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to check product availability', [
                'error' => $e->getMessage(),
                'productId' => $productId
            ]);
            return [
                'success' => false,
                'available' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate API configuration
     */
    public function validateConfiguration()
    {
        $required = [
            'deviceId' => $this->deviceId,
            'email' => $this->email,
            'securityCode' => $this->securityCode,
            'phone' => $this->phone,
            'key' => $this->key,
            'secretKey' => $this->secretKey,
            'secretIv' => $this->secretIv
        ];

        $missing = [];
        foreach ($required as $key => $value) {
            if (empty($value)) {
                $missing[] = $key;
            }
        }

        if (!empty($missing)) {
            throw new \Exception('Missing required configuration: ' . implode(', ', $missing));
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
        Cache::tags(['likecard_products'])->flush();
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
