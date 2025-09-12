<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
