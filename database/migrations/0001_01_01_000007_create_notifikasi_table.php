<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', [
                'surat_masuk', 'surat_diproses', 'surat_selesai', 'surat_ditolak',
                'laporan_masuk', 'laporan_diproses', 'laporan_selesai',
                'permohonan_masuk', 'permohonan_diproses', 'permohonan_selesai', 'permohonan_ditolak',
                'pengumuman',
            ]);
            $table->string('judul');
            $table->text('pesan');
            $table->dateTime('tanggal');
            $table->boolean('dibaca')->default(false);
            $table->string('link')->nullable();
            $table->string('target_role')->nullable();
            $table->unsignedBigInteger('target_user_id')->nullable();
            $table->char('target_nik', 16)->nullable();
            $table->timestamps();

            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['target_role', 'dibaca']);
            $table->index(['target_user_id', 'dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
