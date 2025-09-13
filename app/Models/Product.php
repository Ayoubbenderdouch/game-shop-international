<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
