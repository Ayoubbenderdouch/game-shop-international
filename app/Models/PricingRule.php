<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $type
 * @property numeric $value
 * @property string $apply_to
 * @property int|null $category_id
 * @property int|null $product_id
 * @property bool $is_active
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereApplyTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PricingRule whereValue($value)
 * @mixin \Eloquent
 */
class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'apply_to',
        'category_id',
        'product_id',
        'is_active',
        'priority',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            });
    }

    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at < now()) {
            return false;
        }

        return true;
    }

    public function applyToProducts()
    {
        $query = Product::query();

        switch ($this->apply_to) {
            case 'all':
                // Apply to all products
                break;
            case 'category':
                $query->where('category_id', $this->category_id);
                break;
            case 'product':
                $query->where('id', $this->product_id);
                break;
        }

        $products = $query->get();

        foreach ($products as $product) {
            if ($this->type === 'percentage') {
                $product->margin_percentage = $this->value;
                $product->margin_type = 'percentage';
            } else {
                $product->margin_amount = $this->value;
                $product->margin_type = 'fixed';
            }
            $product->calculateSellingPrice();
            $product->save();
        }

        return $products->count();
    }
}
