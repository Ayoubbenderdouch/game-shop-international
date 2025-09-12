<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'margin_amount',
        'margin_percentage',
        'margin_type',
        'currency',
        'is_available',
        'is_active',
        'stock_quantity',
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
        'margin_amount' => 'decimal:2',
        'margin_percentage' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'optional_fields' => 'array',
        'forbidden_countries' => 'array',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
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
        } else {
            $this->selling_price = $this->cost_price + $this->margin_amount;
            if ($this->cost_price > 0) {
                $this->margin_percentage = ($this->margin_amount / $this->cost_price) * 100;
            }
        }
    }

    public function applyPricingRules()
    {
        $rules = PricingRule::active()
            ->where(function ($query) {
                $query->where('apply_to', 'all')
                    ->orWhere(function ($q) {
                        $q->where('apply_to', 'category')
                          ->where('category_id', $this->category_id);
                    })
                    ->orWhere(function ($q) {
                        $q->where('apply_to', 'product')
                          ->where('product_id', $this->id);
                    });
            })
            ->orderBy('priority', 'desc')
            ->first();

        if ($rules) {
            if ($rules->type === 'percentage') {
                $this->margin_percentage = $rules->value;
                $this->margin_type = 'percentage';
            } else {
                $this->margin_amount = $rules->value;
                $this->margin_type = 'fixed';
            }
            $this->calculateSellingPrice();
            $this->save();
        }
    }

    public function getAverageRating()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    public function getReviewsCount()
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    public function isInStock()
    {
        return is_null($this->stock_quantity) || $this->stock_quantity > 0;
    }

    public function isForbiddenInCountry($country)
    {
        if (empty($this->forbidden_countries)) {
            return false;
        }
        return in_array(strtoupper($country), array_map('strtoupper', $this->forbidden_countries));
    }

    public function hasOptionalFields()
    {
        return !empty($this->optional_fields);
    }

    public function getRequiredOptionalFields()
    {
        if (empty($this->optional_fields)) {
            return [];
        }

        return collect($this->optional_fields)->filter(function ($field) {
            return isset($field['required']) && $field['required'] == '1';
        })->values()->toArray();
    }
}
