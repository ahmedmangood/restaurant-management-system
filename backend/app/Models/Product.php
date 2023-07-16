<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable =[
        "name" ,
        "total_price"  ,
        "image",
        "category_id",
        "extra",
        'discount',
        'description'
    ];
    protected $casts = [
        'extra' => 'array',
    ];
    public function ingredients():BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class,'product_ingredient')->withPivot('quantity', 'total', 'price');
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function orderproduct()
    {
        return $this->hasMany(OrderProduct::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function getStatusofCategory()
    {
        return $this->category->status;
    }

    public function UpdateStaus()
    {
        // if($this->closed)
        // {

        //     $this->status=!$this->status;
        //     return $this->save();

        // }
        if($this->getStatusofCategory())
        {
            foreach ($this->ingredients as $ingredient)
            {
                if($ingredient->status==0 || $ingredient->pivot->quantity > $ingredient->quntity)
                {
                    $this->status = 0 ;
                   return $this->save();
                }
            }
        }else
        {
            $this->status=0;
            return $this->save();
        }
        $this->status=1;
        return $this->save();

    }
}

