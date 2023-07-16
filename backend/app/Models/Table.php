<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $fillable = [
        "number" , "guest_numbers" 
       ];

       public function orders()
       {
           return $this->hasMany(Order::class);
       }
       public function reservations()
       {
           return $this->hasMany(Reservation::class);
       }
}
