<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('end_qty')->default(0)->after('quantity');
            $table->boolean('is_qt_product')->default(true)->after('end_qty');
            $table->string('identifier')->nullable()->after('is_qt_product');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['end_qty', 'is_qt_product', 'identifier']);
        });
    }
};
