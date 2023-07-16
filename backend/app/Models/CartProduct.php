<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{

    use HasFactory;

    protected $table = 'cart_product';
    protected $fillable =[
        'user_id',
        'product_id',
        'total_price',
        'quantity',
        'customer_id'

    ];

    public function product()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }

}
