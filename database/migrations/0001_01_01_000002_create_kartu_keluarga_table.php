<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_keluarga', function (Blueprint $table) {
            $table->char('no_kk', 16)->primary();
            $table->string('kepala_keluarga');
            $table->string('alamat');
            $table->string('rt_rw', 20);
            $table->string('kelurahan')->default('Karangtalun');
            $table->string('kecamatan')->default('Tanon');
            $table->string('kabupaten')->default('Sragen');
            $table->string('provinsi')->default('Jawa Tengah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kartu_keluarga');
    }
};
