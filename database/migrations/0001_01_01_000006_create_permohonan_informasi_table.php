<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_informasi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permohonan')->unique();
            $table->string('nama_pemohon');
            $table->text('alamat_pemohon');
            $table->string('kontak_pemohon');
            $table->text('tujuan_permohonan');
            $table->text('informasi_diminta');
            $table->enum('status', ['Dikirim', 'Diproses', 'Dijawab', 'Ditolak'])->default('Dikirim');
            $table->text('catatan_admin')->nullable();
            $table->unsignedBigInteger('pemohon_user_id')->nullable();
            $table->char('pemohon_nik', 16)->nullable();
            $table->timestamps();

            $table->foreign('pemohon_user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('permohonan_lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_informasi_id')->constrained('permohonan_informasi')->onDelete('cascade');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->enum('tipe', ['lampiran_pemohon', 'balasan_admin'])->default('lampiran_pemohon');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_lampiran');
        Schema::dropIfExists('permohonan_informasi');
    }
};
