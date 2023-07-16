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
        Schema::table('product_ingredient', function (Blueprint $table) {
            DB::statement('CREATE TRIGGER calculate_total BEFORE UPDATE ON product_ingredient FOR EACH ROW
            SET NEW.total = NEW.price * NEW.quantity;
            ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_ingredient', function (Blueprint $table) {
            DB::unprepared('DROP TRIGGER IF EXISTS calculate_total');

        });
    }
};
