<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->restrictOnDelete();
            $table->date('tanggal');
            $table->decimal('jumlah', 12, 3);
            $table->decimal('harga_beli', 12, 2);
            $table->decimal('total_harga', 16, 2);
            $table->timestamps();

            $table->index(['tanggal', 'produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
