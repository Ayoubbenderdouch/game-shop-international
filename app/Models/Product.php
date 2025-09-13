<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $api_id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image
 * @property numeric $cost_price
 * @property numeric $selling_price
 * @property numeric $margin_amount
 * @property numeric $margin_percentage
 * @property string $margin_type
 * @property string $currency
 * @property bool $is_available
 * @property bool $is_active
 * @property int|null $stock_quantity
 * @property array<array-key, mixed>|null $optional_fields
 * @property array<array-key, mixed>|null $forbidden_countries
 * @property string|null $redemption_instructions
 * @property int $sort_order
 * @property int $sales_count
 * @property numeric $vat_percentage
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read mixed $average_rating
 * @property-read mixed $formatted_original_price
 * @property-read mixed $formatted_price
 * @property-read mixed $total_reviews
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PricingRule> $pricingRules
 * @property-read int|null $pricing_rules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product inStock()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCostPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereForbiddenCountries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMarginAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMarginPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMarginType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereOptionalFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereRedemptionInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSalesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStockQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereVatPercentage($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_id',
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'cost_price',
        'selling_price',
        'original_price',
        'margin_amount',
        'margin_percentage',
        'margin_type',
        'currency',
        'is_available',
        'is_active',
        'stock_quantity',
        'sku',
        'optional_fields',
        'forbidden_countries',
        'redemption_instructions',
        'sort_order',
        'sales_count',
        'vat_percentage',
        'metadata',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'margin_amount' => 'decimal:2',
        'margin_percentage' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'optional_fields' => 'array',
        'forbidden_countries' => 'array',
        'metadata' => 'array',
        'stock_quantity' => 'integer',
        'sales_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);

                // Ensure unique slug
                $count = static::whereRaw("slug RLIKE '^{$product->slug}(-[0-9]+)?$'")->count();
                if ($count > 0) {
                    $product->slug = $product->slug . '-' . ($count + 1);
                }
            }
            $product->calculateSellingPrice();
        });

        static::updating(function ($product) {
            if ($product->isDirty(['cost_price', 'margin_amount', 'margin_percentage', 'margin_type'])) {
                $product->calculateSellingPrice();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function pricingRules()
    {
        return $this->hasMany(PricingRule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('stock_quantity')
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    public function calculateSellingPrice()
    {
        if ($this->margin_type === 'percentage') {
            $this->margin_amount = $this->cost_price * ($this->margin_percentage / 100);
            $this->selling_price = $this->cost_price + $this->margin_amount;
        } elseif ($this->margin_type === 'fixed') {
            $this->selling_price = $this->cost_price + $this->margin_amount;
            if ($this->cost_price > 0) {
                $this->margin_percentage = ($this->margin_amount / $this->cost_price) * 100;
            }
        } else {
            $this->selling_price = $this->cost_price;
        }

        // Apply VAT if applicable
        if ($this->vat_percentage > 0) {
            $this->selling_price = $this->selling_price * (1 + $this->vat_percentage / 100);
        }
    }

    public function applyPricingRules()
    {
        $rules = $this->pricingRules()->active()->orderBy('priority')->get();

        foreach ($rules as $rule) {
            if ($rule->isApplicable($this)) {
                $rule->apply($this);
            }
        }
    }

    public function getDiscountPercentage()
    {
        if ($this->original_price && $this->original_price > $this->selling_price) {
            return round((($this->original_price - $this->selling_price) / $this->original_price) * 100);
        }
        return 0;
    }

    public function isForbiddenInCountry($country)
    {
        if (empty($this->forbidden_countries)) {
            return false;
        }
        return in_array($country, $this->forbidden_countries);
    }

    public function hasOptionalFields()
    {
        return !empty($this->optional_fields);
    }

    public function getRequiredOptionalFields()
    {
        if (!$this->hasOptionalFields()) {
            return [];
        }

        return array_filter($this->optional_fields, function ($field) {
            return isset($field['required']) && $field['required'] === true;
        });
    }

    public function updateAverageRating()
    {
        $avgRating = $this->reviews()
            ->where('is_approved', true)
            ->avg('rating');

        $this->update([
            'metadata' => array_merge($this->metadata ?? [], [
                'average_rating' => $avgRating,
                'total_reviews' => $this->reviews()->where('is_approved', true)->count()
            ])
        ]);
    }

    public function getAverageRatingAttribute()
    {
        return $this->metadata['average_rating'] ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->metadata['total_reviews'] ?? 0;
    }

    public function incrementSalesCount($quantity = 1)
    {
        $this->increment('sales_count', $quantity);
    }

    public function decrementStock($quantity = 1)
    {
        if ($this->stock_quantity !== null) {
            $this->decrement('stock_quantity', $quantity);
        }
    }

    public function restoreStock($quantity = 1)
    {
        if ($this->stock_quantity !== null) {
            $this->increment('stock_quantity', $quantity);
        }
    }

    public function isInStock()
    {
        return $this->stock_quantity === null || $this->stock_quantity > 0;
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->selling_price, 2);
    }

    public function getFormattedOriginalPriceAttribute()
    {
        return $this->original_price ? '$' . number_format($this->original_price, 2) : null;
    }
}
