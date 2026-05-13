<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_surat', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_surat', [
                'surat_keterangan_domisili',
                'surat_keterangan_tidak_mampu',
                'surat_pengantar_skck',
                'surat_keterangan_usaha',
            ]);
            $table->string('nomor_tiket')->unique();
            $table->string('nomor_surat')->nullable();
            $table->enum('status', ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'])->default('Menunggu');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_diperbarui')->nullable();
            $table->unsignedBigInteger('diajukan_oleh_user_id');
            $table->char('diajukan_oleh_nik', 16);
            $table->string('diajukan_oleh_nama');
            $table->char('pemohon_nik', 16);
            $table->string('pemohon_nama_lengkap');
            $table->string('pemohon_tempat_lahir');
            $table->date('pemohon_tanggal_lahir');
            $table->string('pemohon_jenis_kelamin');
            $table->string('pemohon_pekerjaan');
            $table->text('pemohon_alamat');
            $table->string('pemohon_status_hubungan');
            $table->text('keperluan');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->foreign('diajukan_oleh_user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('surat_lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_surat_id')->constrained('pengajuan_surat')->onDelete('cascade');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_lampiran');
        Schema::dropIfExists('pengajuan_surat');
    }
};
