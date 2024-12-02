<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    
    protected $fillable =[
        'customer_id',
        'product_id',
        'qty',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function totalPrice()
    {
        return $this->qty = $this->product->price;
    }

    // public function grandTotal($customerId)
    // {
    //    $cartItems = Cart::where('customer_id',$customerId)->get();
       
    //    $total = $cartItemms->sum(function($item){
    //     return $item->totalPrice();
    //    });

    //    return $total;
    // }
    
    public static function grandTotal($customerId)
    {
        $cartItems = Cart::where('customer_id', $customerId)->get();
        $total = $cartItems->sum(function ($item) {
            return $item->totalPrice();
        });

        return $total;
    }
}
