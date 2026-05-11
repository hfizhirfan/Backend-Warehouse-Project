<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();
            $table->string('waybill_number')->nullable();

            $table->string('platform')->nullable();
            $table->string('store')->nullable();     // ✅ pindahin ke sini
            $table->string('courier')->nullable();   // ✅ pindahin ke sini

            $table->string('customer_name')->nullable();

            $table->enum('status', [
                'pending',
                'picked',
                'packed',
                'shipped',
                'cancelled'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
