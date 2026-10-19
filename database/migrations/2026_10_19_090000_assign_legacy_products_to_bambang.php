<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $legacyOwner = DB::table('pemiliks')->where('kode', '000')->first();

        if (! $legacyOwner) {
            return;
        }

        $existingOwner = DB::table('pemiliks')->where('kode', '001')->first();

        if ($existingOwner && $existingOwner->id !== $legacyOwner->id) {
            throw new \RuntimeException('Cannot assign legacy products to ID 001 because it already belongs to another owner.');
        }

        DB::table('pemiliks')
            ->where('id', $legacyOwner->id)
            ->update([
                'kode' => '001',
                'nama' => 'Bambang',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        $bambang = DB::table('pemiliks')->where('kode', '001')->first();

        if (! $bambang) {
            return;
        }

        $existingLegacyOwner = DB::table('pemiliks')->where('kode', '000')->first();

        if ($existingLegacyOwner && $existingLegacyOwner->id !== $bambang->id) {
            throw new \RuntimeException('Cannot restore legacy owner ID 000 because it already belongs to another owner.');
        }

        DB::table('pemiliks')
            ->where('id', $bambang->id)
            ->update([
                'kode' => '000',
                'nama' => 'Data lama (belum dipisahkan)',
                'updated_at' => now(),
            ]);
    }
};
