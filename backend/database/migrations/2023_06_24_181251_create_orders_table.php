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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_price', 8, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('tax', 5, 2)->default(0.14);
            $table->enum('payment_method', ['CASH', 'VISA'])->nullable();
            $table->decimal('service_fee', 5, 2)->default(0.12);
            $table->enum('status', ['Pending', 'Accepted', 'Prepare', 'Complete', 'Served', 'Canceled', 'Paid']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
