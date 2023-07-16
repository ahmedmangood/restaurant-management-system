<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {

            DB::unprepared('CREATE TRIGGER calculate_price AFTER UPDATE ON ingredients
            FOR EACH ROW
            UPDATE product_ingredient
            INNER JOIN ingredients ON product_ingredient.ingredient_id = ingredients.id
            SET product_ingredient.price = ingredients.price + ingredients.price*ingredients.profit
            WHERE ingredients.id = NEW.id;
        ');

    });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
                DB::unprepared('DROP TRIGGER IF EXISTS calculate_price');

            });
    }
};
