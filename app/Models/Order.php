<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $order_number
 * @property string $reference_id
 * @property string|null $api_order_id
 * @property int $user_id
 * @property numeric $subtotal
 * @property numeric $vat_amount
 * @property numeric $total_amount
 * @property string $currency
 * @property string $status
 * @property string $payment_status
 * @property string|null $payment_method
 * @property string|null $payment_intent_id
 * @property array<array-key, mixed>|null $payment_data
 * @property string|null $notes
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order failed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order processing()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereApiOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentIntentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereVatAmount($value)
 * @mixin \Eloquent
 */
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
