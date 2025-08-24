<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PubgUcOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'player_id',
        'uc_amount',
        'price',
        'status',
        'transaction_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
