<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property string $product_name
 * @property string $product_api_id
 * @property int $quantity
 * @property numeric $cost_price
 * @property numeric $selling_price
 * @property numeric $total_price
 * @property array<array-key, mixed>|null $optional_fields_data
 * @property array<array-key, mixed>|null $serials
 * @property string|null $serial_code
 * @property string|null $serial_number
 * @property \Illuminate\Support\Carbon|null $valid_to
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCostPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOptionalFieldsData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSerialCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSerials($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereValidTo($value)
 * @mixin \Eloquent
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_api_id',
        'quantity',
        'cost_price',
        'selling_price',
        'total_price',
        'optional_fields_data',
        'serials',
        'serial_code',
        'serial_number',
        'valid_to',
        'status',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'optional_fields_data' => 'array',
        'serials' => 'array',
        'valid_to' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function markAsDelivered($serials = null)
    {
        $data = ['status' => 'delivered'];

        if ($serials) {
            $data['serials'] = $serials;

            if (isset($serials[0])) {
                $serial = $serials[0];
                $data['serial_code'] = $serial['serialCode'] ?? null;
                $data['serial_number'] = $serial['serialNumber'] ?? null;
                $data['valid_to'] = isset($serial['validTo']) ? \Carbon\Carbon::parse($serial['validTo']) : null;
            }
        }

        $this->update($data);
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }

    public function getDecryptedSerialCode()
    {
        if (!$this->serial_code) {
            return null;
        }

        // This would use the decryption logic from the API documentation
        // For now returning as-is since we need the actual secret keys
        return $this->serial_code;
    }

    public function calculateTotalPrice()
    {
        $this->total_price = $this->selling_price * $this->quantity;
        $this->save();
    }
}
