<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
