<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_ingredient', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained();
            $table->foreignId('ingredient_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_ingredient', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            $table->dropForeign(['ingredient_id']);
            $table->dropColumn('ingredient_id');
        });
    }
};
