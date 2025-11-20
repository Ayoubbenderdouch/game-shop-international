<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MazayaOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mazaya_order_id',
        'uuid',
        'mazaya_product_id',
        'product_name',
        'game_name',
        'player_id',
        'player_name',
        'quantity',
        'price',
        'status',
        'customer_data',
        'admin_data',
        'response_message',
        'completed_at',
    ];

    protected $casts = [
        'customer_data' => 'array',
        'admin_data' => 'array',
        'completed_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->uuid) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if order is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if order is processing
     */
    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    /**
     * Check if order is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if order is failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if order is canceled
     */
    public function isCanceled()
    {
        return $this->status === 'canceled';
    }

    /**
     * Mark order as processing
     */
    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    /**
     * Mark order as completed
     */
    public function markAsCompleted($adminData = null)
    {
        $this->update([
            'status' => 'completed',
            'admin_data' => $adminData,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark order as failed
     */
    public function markAsFailed($message = null)
    {
        $this->update([
            'status' => 'failed',
            'response_message' => $message,
        ]);
    }

    /**
     * Mark order as canceled
     */
    public function markAsCanceled($message = null)
    {
        $this->update([
            'status' => 'canceled',
            'response_message' => $message,
        ]);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'processing' => 'جاري المعالجة',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'canceled' => 'ملغي',
            default => $this->status,
        };
    }

    /**
     * Scope for user orders
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for game
     */
    public function scopeGame($query, $gameName)
    {
        return $query->where('game_name', $gameName);
    }
}
