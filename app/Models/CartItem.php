<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'optional_fields_data',
    ];

    protected $casts = [
        'optional_fields_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalPrice()
    {
        return $this->product->selling_price * $this->quantity;
    }

    public function incrementQuantity($amount = 1)
    {
        $this->increment('quantity', $amount);
    }

    public function decrementQuantity($amount = 1)
    {
        if ($this->quantity > $amount) {
            $this->decrement('quantity', $amount);
        } else {
            $this->delete();
        }
    }

    public function updateQuantity($quantity)
    {
        if ($quantity <= 0) {
            $this->delete();
        } else {
            $this->update(['quantity' => $quantity]);
        }
    }
}
