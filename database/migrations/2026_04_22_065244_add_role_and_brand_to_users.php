<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('guest'); // super_admin, admin, guest
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // drop foreign key dulu
            $table->dropForeign(['brand_id']);

            // drop kolom
            $table->dropColumn(['role', 'brand_id']);
        });
    }
};
