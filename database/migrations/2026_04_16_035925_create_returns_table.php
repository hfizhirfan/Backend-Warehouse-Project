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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();

            $table->string('waybill');
            $table->string('ekspedisi');
            $table->string('platform');

            $table->unsignedBigInteger('product_id'); // SKU
            $table->integer('qty')->default(1);

            $table->enum('condition', ['good', 'defect', 'lost']);

            $table->string('status')->nullable();
            // contoh: good_stock, defect, lost

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
