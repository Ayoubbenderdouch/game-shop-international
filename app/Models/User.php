<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

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
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'wallet_balance' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
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

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function getCartTotal()
    {
        return $this->cartItems->sum(function ($item) {
            return $item->product->selling_price * $item->quantity;
        });
    }

    public function hasInCart($productId)
    {
        return $this->cartItems()->where('product_id', $productId)->exists();
    }

    public function hasFavorited($productId)
    {
        return $this->favorites()->where('product_id', $productId)->exists();
    }

    public function hasReviewed($productId)
    {
        return $this->reviews()->where('product_id', $productId)->exists();
    }

    public function canReview($productId)
    {
        return $this->orders()
            ->whereHas('orderItems', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->where('status', 'completed')
            ->exists();
    }
}
