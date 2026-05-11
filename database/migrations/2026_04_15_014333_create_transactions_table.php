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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->enum('type', [
                'inbound',
                'outbound',
                'return',
                'adjustment',
                'bundling'
            ]);

            $table->integer('quantity');
            $table->integer('qty_signed');

            $table->string('reference_type')->nullable();
            $table->string('reference_number')->nullable();

            $table->string('supplier')->nullable();
            $table->string('customer')->nullable();

            $table->string('platform')->nullable();
            $table->string('store')->nullable();
            $table->string('courier')->nullable();

            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            $table->string('return_condition')->nullable();

            $table->text('remark')->nullable();
            $table->string('created_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
