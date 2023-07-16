<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class OrderProduct extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'order_product';
    protected $fillable = [
        "total_price" , "quantity" , "order_id" , "product_id","image",
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // public function ingredients()
    // {
    //     return $this->belongsToMany(Ingredient::class, 'product_ingredient')->withPivot('quantity');
    // }
    
}
