<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemiliks', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama', 150);
            $table->timestamps();
        });

        $legacyOwnerId = DB::table('pemiliks')->insertGetId([
            'kode' => '000',
            'nama' => 'Data lama (belum dipisahkan)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('produks', function (Blueprint $table) {
            $table->foreignId('pemilik_id')
                ->nullable()
                ->after('id')
                ->constrained('pemiliks')
                ->restrictOnDelete();
        });

        DB::table('produks')->update(['pemilik_id' => $legacyOwnerId]);

        Schema::table('produks', function (Blueprint $table) {
            $table->dropUnique('produks_nama_unique');
            $table->unique(['pemilik_id', 'nama']);
        });
    }

    public function down(): void
    {
        if (DB::table('produks')->select('nama')->groupBy('nama')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new \RuntimeException('Cannot roll back owner separation while product names are duplicated across owners.');
        }

        Schema::table('produks', function (Blueprint $table) {
            $table->dropUnique('produks_pemilik_id_nama_unique');
            $table->dropForeign(['pemilik_id']);
            $table->dropColumn('pemilik_id');
            $table->unique('nama');
        });

        Schema::dropIfExists('pemiliks');
    }
};
