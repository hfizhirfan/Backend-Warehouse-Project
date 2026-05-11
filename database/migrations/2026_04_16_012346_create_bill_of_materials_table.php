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
        Schema::create('bill_of_materials', function (Blueprint $table) {
            $table->id();

            // Produk hasil bundling
            $table->foreignId('bundle_product_id')->constrained('products')->cascadeOnDelete();

            // Produk komponen
            $table->foreignId('component_product_id')->constrained('products')->cascadeOnDelete();

            // Qty komponen yang dibutuhkan
            $table->integer('qty');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_materials');
    }
};
