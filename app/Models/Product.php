<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'price',
        'description',
        'image_url',
        'category_id',
        'tags',
        'country_availability',
        'is_active',
        'is_preorder',
        'stock_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'country_availability' => 'array',
        'is_active' => 'boolean',
        'is_preorder' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productCodes()
    {
        return $this->hasMany(ProductCode::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAvailableStockAttribute()
    {
        return $this->productCodes()->where('is_used', false)->count();
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_deleted', false)->avg('rating') ?? 0;
    }
}
