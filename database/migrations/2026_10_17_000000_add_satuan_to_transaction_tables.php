<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->string('satuan', 30)->nullable();
        });

        Schema::table('penjuwalans', function (Blueprint $table) {
            $table->string('satuan', 30)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });

        Schema::table('penjuwalans', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });
    }
};
