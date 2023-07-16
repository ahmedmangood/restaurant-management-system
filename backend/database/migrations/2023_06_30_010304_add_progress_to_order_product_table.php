<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_product', function (Blueprint $table) {
            $table->integer('progress')->nullable();
        });
        DB::statement("ALTER TABLE order_product MODIFY COLUMN status ENUM('Complete', 'Cancel', 'Progress') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_product', function (Blueprint $table) {
            $table->dropColumn('progress');
        });
        DB::statement("ALTER TABLE order_product MODIFY COLUMN status ENUM('Complete', 'Cancel') DEFAULT NULL");
    }
};
