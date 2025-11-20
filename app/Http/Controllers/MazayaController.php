<?php

namespace App\Http\Controllers;

use App\Models\MazayaOrder;
use App\Services\MazayaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MazayaController extends Controller
{
    protected $mazayaService;

    public function __construct(MazayaService $mazayaService)
    {
        $this->mazayaService = $mazayaService;
    }

    /**
     * Show Mazaya products page
     */
    public function index()
    {
        $categories = $this->mazayaService->getCategories();
        $supportedGames = $this->mazayaService->getSupportedGames();

        // Get all products from gaming categories
        $products = [];

        // Get subcategories first (games are subcategories)
        foreach ($categories as $category) {
            // Get subcategories (e.g., PUBG, Free Fire, etc.)
            $response = \Illuminate\Support\Facades\Http::get(config('services.mazaya.base_url') . '/categories/' . $category['id']);

            if ($response->successful() && $response->json('status') === 'success') {
                $subcategories = $response->json('categories', []);

                // Get products from each subcategory
                foreach ($subcategories as $subcat) {
                    $categoryProducts = $this->mazayaService->getProductsByCategory($subcat['id']);
                    $products = array_merge($products, $categoryProducts);
                }
            }
        }

        // Group products by game
        $gameProducts = $this->groupProductsByGame($products);

        // For now, use simple index page with just the 6 games
        return view('mazaya.index-simple');
    }

    /**
     * Show region selection for a game
     */
    public function selectRegion($gameSlug)
    {
        // Map game slugs to their category info and regions
        $gameMapping = [
            'pubg-mobile-direct' => [
                'name' => 'PUBG Mobile',
                'regions' => [
                    ['id' => 4, 'name' => 'Global', 'name_ar' => 'عالمي', 'slug' => 'global', 'image' => 'PUBG realod.png'],
                ]
            ],
            'free-fire-direct' => [
                'name' => 'Free Fire',
                'regions' => [
                    ['id' => 5, 'name' => 'Global', 'name_ar' => 'عالمي', 'slug' => 'global', 'image' => 'FREE FIRE realod.png'],
                ]
            ],
            'mobile-legends-direct' => [
                'name' => 'Mobile Legends',
                'regions' => [
                    ['id' => 172, 'name' => 'Asia', 'name_ar' => 'آسيا', 'slug' => 'asia', 'image' => 'mobile-legends-direct.png'],
                    ['id' => 253, 'name' => 'Global', 'name_ar' => 'عالمي', 'slug' => 'global', 'image' => 'mobile-legends-direct.png'],
                ]
            ],
            'yalla-ludo-direct' => [
                'name' => 'Yalla Ludo',
                'regions' => [
                    ['id' => 185, 'name' => 'Global', 'name_ar' => 'عالمي', 'slug' => 'global', 'image' => 'yala ludo realod.png'],
                ]
            ],
            'genshin-impact-direct' => [
                'name' => 'Genshin Impact',
                'regions' => [
                    ['id' => 171, 'name' => 'Global', 'name_ar' => 'عالمي', 'slug' => 'global', 'image' => 'genshin impact realod.png'],
                ]
            ],
            'fc-mobile-direct' => [
                'name' => 'FC Mobile',
                'regions' => [
                    ['id' => 12, 'name' => 'KSA', 'name_ar' => 'السعودية', 'slug' => 'ksa', 'image' => 'fc-mobile-direct.png'],
                ]
            ],
        ];

        if (!isset($gameMapping[$gameSlug])) {
            return redirect()->route('home')->with('error', 'Game not found');
        }

        $game = $gameMapping[$gameSlug];
        $gameName = $game['name'];
        $regions = $game['regions'];

        return view('mazaya.select-region', compact('gameName', 'regions', 'gameSlug'));
    }

    /**
     * Show amount selection for a game region
     */
    public function selectAmount($gameSlug, $regionSlug)
    {
        // Map game slugs to their category info
        $gameMapping = [
            'pubg-mobile-direct' => [
                'name' => 'PUBG Mobile',
                'regions' => ['global' => ['id' => 4, 'name' => 'Global', 'name_ar' => 'عالمي']],
            ],
            'free-fire-direct' => [
                'name' => 'Free Fire',
                'regions' => ['global' => ['id' => 5, 'name' => 'Global', 'name_ar' => 'عالمي']],
            ],
            'mobile-legends-direct' => [
                'name' => 'Mobile Legends',
                'regions' => [
                    'asia' => ['id' => 172, 'name' => 'Asia', 'name_ar' => 'آسيا'],
                    'global' => ['id' => 253, 'name' => 'Global', 'name_ar' => 'عالمي'],
                ],
            ],
            'yalla-ludo-direct' => [
                'name' => 'Yalla Ludo',
                'regions' => ['global' => ['id' => 185, 'name' => 'Global', 'name_ar' => 'عالمي']],
            ],
            'genshin-impact-direct' => [
                'name' => 'Genshin Impact',
                'regions' => ['global' => ['id' => 171, 'name' => 'Global', 'name_ar' => 'عالمي']],
            ],
            'fc-mobile-direct' => [
                'name' => 'FC Mobile',
                'regions' => ['ksa' => ['id' => 12, 'name' => 'KSA', 'name_ar' => 'السعودية']],
            ],
        ];

        if (!isset($gameMapping[$gameSlug]) || !isset($gameMapping[$gameSlug]['regions'][$regionSlug])) {
            return redirect()->route('home')->with('error', 'Game or region not found');
        }

        $game = $gameMapping[$gameSlug];
        $region = $game['regions'][$regionSlug];
        $categoryId = $region['id'];

        // Get products for this category
        $products = $this->mazayaService->getProductsByCategory($categoryId);

        return view('mazaya.select-amount', compact('products', 'game', 'region', 'gameSlug', 'regionSlug'));
    }

    /**
     * Show product details
     */
    public function show($productId)
    {
        $product = $this->mazayaService->getProductById($productId);

        if (!$product) {
            return redirect()->route('mazaya.index')->with('error', 'Product not found');
        }

        return view('mazaya.show', compact('product'));
    }

    /**
     * Process order
     */
    public function order(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'player_id' => 'required|string|max:255',
            'player_name' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
        ]);

        try {
            $product = $this->mazayaService->getProductById($request->product_id);

            if (!$product) {
                return back()->with('error', 'Product not found');
            }

            // Create local order record
            $order = MazayaOrder::create([
                'user_id' => Auth::id(),
                'mazaya_product_id' => $product['id'],
                'product_name' => $product['name'],
                'game_name' => $this->extractGameName($product['name']),
                'player_id' => $request->player_id,
                'player_name' => $request->player_name,
                'quantity' => $request->quantity ?? 1,
                'price' => 0, // Will be updated after order
                'status' => 'pending',
            ]);

            // Create order on Mazaya
            $result = $this->mazayaService->createOrder(
                $request->product_id,
                $request->player_id,
                $request->quantity ?? 1,
                $request->player_name,
                $order->uuid
            );

            if ($result['success']) {
                $mazayaOrder = $result['order'];

                $order->update([
                    'mazaya_order_id' => $mazayaOrder['id'] ?? null,
                    'price' => $mazayaOrder['price'] ?? 0,
                    'status' => $this->mapMazayaStatus($mazayaOrder['status'] ?? 'pending'),
                    'customer_data' => $mazayaOrder['customer_data'] ?? null,
                    'admin_data' => $mazayaOrder['admin_data'] ?? null,
                ]);

                return redirect()->route('mazaya.order.status', $order->id)
                    ->with('success', 'Order created successfully! Status: ' . $order->status_label);
            } else {
                $order->markAsFailed($result['message']);
                return back()->with('error', 'Order failed: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Mazaya order error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing your order');
        }
    }

    /**
     * Show order status
     */
    public function orderStatus($orderId)
    {
        $order = MazayaOrder::findOrFail($orderId);

        // Check if user owns this order (if authenticated)
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('mazaya.order-status', compact('order'));
    }

    /**
     * Check order status from Mazaya API
     */
    public function checkOrderStatus($orderId)
    {
        $order = MazayaOrder::findOrFail($orderId);

        if (!$order->mazaya_order_id) {
            return response()->json(['error' => 'Mazaya order ID not found'], 404);
        }

        $result = $this->mazayaService->getOrderStatus($order->mazaya_order_id);

        if ($result['success']) {
            $mazayaOrder = $result['order'];

            $order->update([
                'status' => $this->mapMazayaStatus($mazayaOrder['status'] ?? 'pending'),
                'price' => $mazayaOrder['price'] ?? $order->price,
                'customer_data' => $mazayaOrder['customer_data'] ?? $order->customer_data,
                'admin_data' => $mazayaOrder['admin_data'] ?? $order->admin_data,
                'completed_at' => $mazayaOrder['status'] === 'completed' ? now() : $order->completed_at,
            ]);

            return response()->json([
                'success' => true,
                'order' => $order,
                'status_label' => $order->status_label,
            ]);
        }

        return response()->json(['error' => 'Failed to fetch order status'], 500);
    }

    /**
     * Show user's orders
     */
    public function myOrders()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $orders = MazayaOrder::forUser(Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('mazaya.my-orders', compact('orders'));
    }

    /**
     * Get balance
     */
    public function getBalance()
    {
        $result = $this->mazayaService->getBalance();

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'balance' => $result['balance'],
                'user' => $result['user'],
            ]);
        }

        return response()->json(['error' => 'Failed to fetch balance'], 500);
    }

    /**
     * Group products by game name
     */
    private function groupProductsByGame($products)
    {
        $grouped = [];

        foreach ($products as $product) {
            $gameName = $this->extractGameName($product['name']);

            if (!isset($grouped[$gameName])) {
                $grouped[$gameName] = [];
            }

            $grouped[$gameName][] = $product;
        }

        return $grouped;
    }

    /**
     * Extract game name from product name
     */
    private function extractGameName($productName)
    {
        $games = [
            'PUBG Mobile' => ['pubg', 'ببجي', 'بوبجي'],
            'Free Fire' => ['free fire', 'فري فاير', 'فري'],
            'FC Mobile' => ['fc mobile', 'fc', 'fifa mobile'],
            'Yalla Ludo' => ['yalla ludo', 'يلا لودو'],
            'Genshin Impact' => ['genshin', 'جنشن'],
            'Mobile Legends' => ['mobile legend', 'موبايل ليجند', 'ml'],
        ];

        $productLower = mb_strtolower($productName);

        foreach ($games as $game => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($productLower, $keyword) !== false) {
                    return $game;
                }
            }
        }

        return 'Other';
    }

    /**
     * Map Mazaya status to local status
     */
    private function mapMazayaStatus($mazayaStatus)
    {
        return match($mazayaStatus) {
            'complete', 'completed' => 'completed',
            'pending' => 'pending',
            'processing' => 'processing',
            'canceled' => 'canceled',
            'failed' => 'failed',
            default => 'pending',
        };
    }
}
