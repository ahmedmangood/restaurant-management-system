<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable =[
        'name',
        'image',
        'created_at',
        'status'
    ];

    public function products():HasMany
    {
        return $this->hasMany(Product::class);
    }

}
