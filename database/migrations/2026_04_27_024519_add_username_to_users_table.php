<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // tambah kolom username (sementara nullable dulu)
            $table->string('username')->nullable()->after('name');
        });

        // 🔥 isi username dari name (biar data lama tidak null)
        DB::table('users')->update([
            'username' => DB::raw("LOWER(REPLACE(name, ' ', ''))")
        ]);

        Schema::table('users', function (Blueprint $table) {
            // setelah terisi, jadikan unique + tidak boleh null
            $table->string('username')->nullable(false)->change();
            $table->unique('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
