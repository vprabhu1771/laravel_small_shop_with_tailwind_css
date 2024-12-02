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
        'qty',
        'unit_price',
        'sub_total'

    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }    

    public static function boot()
    {
        parent::boot();

        // Automatically calculate total price based on price and quantity
        static::creating(function ($orderItem) {
            $orderItem->sub_total = $orderItem->unit_price * $orderItem->qty;
        });
    }

    /**
     * Accessor for dynamically calculating the subtotal.
     *
     * @return float
     */
    public function getSubtotalAttribute()
    {
        return $this->unit_price * $this->qty;
    }
}
