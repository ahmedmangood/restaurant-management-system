<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'customer_id'
    ];
    public function cartProduct()
    {
        if(Auth::guard('customers')->id()){
            return $this->hasMany(CartProduct::class,'customer_id','customer_id'); //
        }
        return $this->hasMany(CartProduct::class,'user_id','user_id'); //

    }
}
