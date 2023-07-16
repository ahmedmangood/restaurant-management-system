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
        Schema::table('products', function (Blueprint $table) {
            DB::unprepared('CREATE TRIGGER update_total_sum AFTER UPDATE ON product_ingredient FOR EACH ROW
                UPDATE products
                SET total_price = (SELECT SUM(total) FROM product_ingredient WHERE product_id = NEW.product_id)
                WHERE id = NEW.product_id;
            ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            DB::unprepared('DROP TRIGGER IF EXISTS  update_total_sum ');

        });
    }
};
