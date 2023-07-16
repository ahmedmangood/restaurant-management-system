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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained();      
            $table->foreignId('table_id')->constrained();  
            $table->foreignId('customer_id')->constrained()->nullable(); 
        });
            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id'); 
            $table->dropForeign(['table_id']);
            $table->dropColumn('table_id'); 
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id'); 
        });
    }
};
