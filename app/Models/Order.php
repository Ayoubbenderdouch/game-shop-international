<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'reference_id',
        'api_order_id',
        'user_id',
        'subtotal',
        'vat_amount',
        'total_amount',
        'currency',
        'status',
        'payment_status',
        'payment_method',
        'payment_intent_id',
        'payment_data',
        'notes',
        'metadata',
        'paid_at',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_data' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
            if (empty($order->reference_id)) {
                $order->reference_id = 'REF-' . time() . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBeRefunded()
    {
        return $this->status === 'completed' && $this->payment_status === 'paid';
    }

    public function markAsPaid()
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'status' => 'processing',
        ]);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed($reason = null)
    {
        $metadata = $this->metadata ?? [];
        if ($reason) {
            $metadata['failure_reason'] = $reason;
        }

        $this->update([
            'status' => 'failed',
            'payment_status' => 'failed',
            'metadata' => $metadata,
        ]);
    }

    public function calculateTotals()
    {
        $subtotal = 0;
        $vatAmount = 0;

        foreach ($this->orderItems as $item) {
            $subtotal += $item->total_price;
            if ($item->product->vat_percentage > 0) {
                $vatAmount += $item->total_price * ($item->product->vat_percentage / 100);
            }
        }

        $this->subtotal = $subtotal;
        $this->vat_amount = $vatAmount;
        $this->total_amount = $subtotal + $vatAmount;
        $this->save();
    }

    public function getStatusBadgeClass()
    {
        return [
            'pending' => 'bg-yellow-500/20 text-yellow-500',
            'processing' => 'bg-blue-500/20 text-blue-500',
            'completed' => 'bg-green-500/20 text-green-500',
            'failed' => 'bg-red-500/20 text-red-500',
            'refunded' => 'bg-purple-500/20 text-purple-500',
            'cancelled' => 'bg-gray-500/20 text-gray-500',
        ][$this->status] ?? 'bg-gray-500/20 text-gray-500';
    }
}
