<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_kk', function (Blueprint $table) {
            $table->id();
            $table->char('no_kk', 16);
            $table->char('nik', 16)->unique();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->enum('pendidikan', [
                'Tidak/Belum Sekolah', 'SD/Sederajat', 'SMP/Sederajat',
                'SMA/Sederajat', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'
            ]);
            $table->string('pekerjaan');
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
            $table->enum('status_hubungan', [
                'Kepala Keluarga', 'Istri', 'Anak', 'Menantu',
                'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Lainnya'
            ]);
            $table->string('kewarganegaraan', 10)->default('WNI');
            $table->timestamps();

            $table->foreign('no_kk')->references('no_kk')->on('kartu_keluarga')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_kk');
    }
};
