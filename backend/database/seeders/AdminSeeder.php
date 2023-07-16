<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'=>"sondos",
            'email'=>'sondos@gmail.com',
            'password'=>'123!Asdf',
            'phone'=>'01024599887',
            'role'=>'Admin'
        ]);
    }
}
