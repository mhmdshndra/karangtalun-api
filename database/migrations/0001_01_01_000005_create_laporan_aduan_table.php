<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_aduan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket')->unique();
            $table->enum('kategori', ['infrastruktur', 'kamtibmas', 'umum']);
            $table->string('nama_pelapor');
            $table->string('alamat_pelapor');
            $table->string('kontak_pelapor');
            $table->text('deskripsi');
            $table->text('lokasi_kejadian');
            $table->string('lokasi_gps')->nullable();
            $table->enum('status', ['Dikirim', 'Ditindaklanjuti', 'Selesai'])->default('Dikirim');
            $table->text('catatan_admin')->nullable();
            $table->unsignedBigInteger('pelapor_user_id')->nullable();
            $table->char('pelapor_nik', 16)->nullable();
            $table->timestamps();

            $table->foreign('pelapor_user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('laporan_lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_aduan_id')->constrained('laporan_aduan')->onDelete('cascade');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_lampiran');
        Schema::dropIfExists('laporan_aduan');
    }
};
