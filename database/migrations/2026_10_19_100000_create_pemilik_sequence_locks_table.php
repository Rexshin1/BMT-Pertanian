<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemilik_sequence_locks', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
        });

        DB::table('pemilik_sequence_locks')->insert(['id' => 1]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pemilik_sequence_locks');
    }
};
