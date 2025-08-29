<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeFireOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'player_id',
        'diamond_amount',
        'price',
        'status',
        'transaction_id',
        'redemption_code',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
