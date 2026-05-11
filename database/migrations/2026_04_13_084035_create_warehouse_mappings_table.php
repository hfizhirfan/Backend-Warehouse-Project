<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('warehouse_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_code'); // JX, SP, dll
            $table->string('logistics_provider'); // JNT, SPX, dll
            $table->string('platform'); // Shopee, Tiktok, dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_mappings');
    }
};
