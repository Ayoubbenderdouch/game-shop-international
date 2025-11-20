<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string $role
 * @property bool $is_active
 * @property string $locale
 * @property numeric $wallet_balance
 * @property string|null $country
 * @property string|null $city
 * @property string|null $address
 * @property string|null $verification_token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User customers()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerificationToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWalletBalance($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'locale',
        'wallet_balance',
        'country',
        'city',
        'address',
        'verification_token',
        'country_code',
        'currency',
        'timezone',
        'phone_country_code',
        'preferred_language',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'wallet_balance' => 'decimal:2',
        ];
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer' || $this->role === null;
    }

    /**
     * Get user's cart items
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get user's orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get user's favorites
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get user's reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get user's wallet transactions
     */
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get wallet balance
     */
    public function getWalletBalance(): float
    {
        return (float) $this->wallet_balance ?? 0;
    }

    /**
     * Check if user can afford an amount
     */
    public function canAfford(float $amount): bool
    {
        return $this->getWalletBalance() >= $amount;
    }

    /**
     * Add funds to wallet
     */
    public function addToWallet(float $amount, string $description = 'Deposit', string $paymentMethod = null, string $referenceId = null): WalletTransaction
    {
        DB::beginTransaction();
        try {
            // Update wallet balance
            $this->wallet_balance = $this->wallet_balance + $amount;
            $this->save();

            // Create transaction record
            $transaction = $this->walletTransactions()->create([
                'type' => 'deposit',
                'amount' => $amount,
                'balance_after' => $this->wallet_balance,
                'description' => $description,
                'status' => 'completed',
                'payment_method' => $paymentMethod,
                'reference_id' => $referenceId,
            ]);

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Deduct funds from wallet
     */
    public function deductFromWallet(float $amount, string $description = 'Purchase', string $referenceId = null): WalletTransaction
    {
        if (!$this->canAfford($amount)) {
            throw new \Exception('Insufficient wallet balance');
        }

        DB::beginTransaction();
        try {
            // Update wallet balance
            $this->wallet_balance = $this->wallet_balance - $amount;
            $this->save();

            // Create transaction record
            $transaction = $this->walletTransactions()->create([
                'type' => 'purchase',
                'amount' => $amount,
                'balance_after' => $this->wallet_balance,
                'description' => $description,
                'status' => 'completed',
                'reference_id' => $referenceId,
            ]);

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Refund to wallet
     */
    public function refundToWallet(float $amount, string $description = 'Refund', string $referenceId = null): WalletTransaction
    {
        DB::beginTransaction();
        try {
            // Update wallet balance
            $this->wallet_balance = $this->wallet_balance + $amount;
            $this->save();

            // Create transaction record
            $transaction = $this->walletTransactions()->create([
                'type' => 'refund',
                'amount' => $amount,
                'balance_after' => $this->wallet_balance,
                'description' => $description,
                'status' => 'completed',
                'reference_id' => $referenceId,
            ]);

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get formatted wallet balance
     */
    public function getFormattedWalletBalanceAttribute(): string
    {
        return number_format($this->wallet_balance, 2) . ' ' . config('app.currency', 'EUR');
    }

    /**
     * Get cart total
     */
    public function getCartTotal()
    {
        return $this->cartItems()
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->sum(DB::raw('cart_items.quantity * products.selling_price'));
    }

    /**
     * Get cart count
     */
    public function getCartCount()
    {
        return $this->cartItems()->sum('quantity');
    }

    /**
     * Check if user has purchased a product
     */
    public function hasPurchased($productId): bool
    {
        return $this->orders()
            ->where('status', 'completed')
            ->whereHas('orderItems', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();
    }

    /**
     * Check if user has reviewed a product
     */
    public function hasReviewed($productId): bool
    {
        return $this->reviews()
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Check if user can review a product
     */
    public function canReview($productId): bool
    {
        // User can review if they have purchased and haven't reviewed yet
        if (!$this->hasPurchased($productId)) {
            return false;
        }

        return !$this->hasReviewed($productId);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for customer users
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer')
                     ->orWhereNull('role');
    }

    /**
     * Get user's country information
     */
    public function getCountryAttribute()
    {
        if (!$this->country_code) {
            return null;
        }

        return Country::where('code', $this->country_code)->first();
    }

    /**
     * Get user's currency information
     */
    public function getCurrencyRateAttribute()
    {
        if (!$this->currency) {
            return null;
        }

        return CurrencyRate::where('currency', $this->currency)->first();
    }

    /**
     * Get user's preferred language name
     */
    public function getPreferredLanguageNameAttribute()
    {
        $languages = [
            'en' => 'English',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            'it' => 'Italiano',
            'ar' => 'العربية',
        ];

        return $languages[$this->preferred_language ?? 'en'] ?? 'English';
    }

    /**
     * Format price for user's currency
     */
    public function formatPrice($price)
    {
        $currencyService = app(\App\Services\CurrencyService::class);
        return $currencyService->formatPrice($price, $this->currency);
    }

    /**
     * Convert price from USD to user's currency
     */
    public function convertPrice($usdPrice)
    {
        $currencyService = app(\App\Services\CurrencyService::class);
        return $currencyService->convertPrice($usdPrice, $this->currency);
    }

    /**
     * Get user's timezone
     */
    public function getTimezoneAttribute($value)
    {
        return $value ?? 'UTC';
    }

    /**
     * Format date/time according to user's timezone
     */
    public function formatDateTime($datetime, $format = 'Y-m-d H:i:s')
    {
        if (!$datetime) {
            return null;
        }

        return $datetime->setTimezone($this->timezone)->format($format);
    }
}

