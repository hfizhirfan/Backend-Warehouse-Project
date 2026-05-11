<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_mappings', function (Blueprint $table) {
            $table->foreignId('product_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();

            $table->integer('qty_default')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_mappings', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'qty_default']);
        });
    }
};
