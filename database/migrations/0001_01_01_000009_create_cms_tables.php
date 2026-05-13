<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_identitas_desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa');
            $table->string('kode_desa');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->string('provinsi');
            $table->string('kode_pos', 10);
            $table->text('alamat');
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->text('maps_url')->nullable();
            $table->decimal('koordinat_lat', 10, 7)->nullable();
            $table->decimal('koordinat_lng', 10, 7)->nullable();
            $table->string('nama_kades')->nullable();
            $table->string('jabatan_kades')->nullable();
            $table->string('tahun_anggaran', 4)->nullable();
            $table->string('sosmed_facebook')->nullable();
            $table->string('sosmed_instagram')->nullable();
            $table->string('sosmed_twitter')->nullable();
            $table->string('sosmed_youtube')->nullable();
            $table->string('sosmed_tiktok')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_profil_desa', function (Blueprint $table) {
            $table->id();
            $table->text('sejarah')->nullable();
            $table->text('visi')->nullable();
            $table->json('misi')->nullable();
            $table->text('potensi')->nullable();
            $table->text('sambutan')->nullable();
            $table->string('foto_kades')->nullable();
            $table->text('struktur_pemerintahan')->nullable();
            $table->text('fasilitas_teks')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori');
            $table->string('penulis');
            $table->string('tanggal');
            $table->string('waktu')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->enum('status', ['Terbit', 'Draft', 'Diarsipkan'])->default('Draft');
            $table->enum('tipe', ['Artikel', 'Video'])->default('Artikel');
            $table->string('link_video')->nullable();
            $table->string('thumbnail')->nullable();
            $table->longText('konten');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('cms_galeri', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('url');
            $table->string('kategori');
            $table->string('tanggal');
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_umkm', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('kategori');
            $table->string('nama_penjual');
            $table->string('rt_rw');
            $table->string('whatsapp');
            $table->unsignedInteger('harga')->default(0);
            $table->string('foto')->nullable();
            $table->text('deskripsi');
            $table->unsignedInteger('likes')->default(0);
            $table->boolean('aktif')->default(true);
            $table->boolean('unggulan')->default(false);
            $table->timestamps();
        });

        Schema::create('cms_aparatur', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('foto')->nullable();
            $table->string('kategori_jabatan');
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_potensi_desa', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('gambar')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('label')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_titik_lokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_fasilitas_id')->constrained('cms_fasilitas')->onDelete('cascade');
            $table->string('nama');
            $table->string('label')->nullable();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('route_link')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_layanan_publik', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi');
            $table->string('kategori');
            $table->string('estimasi_waktu')->nullable();
            $table->string('biaya')->nullable();
            $table->json('persyaratan')->nullable();
            $table->json('prosedur')->nullable();
            $table->boolean('aktif')->default(true);
            $table->boolean('butuh_login')->default(false);
            $table->text('instruksi')->nullable();
            $table->string('route_slug');
            $table->enum('tipe_layanan', ['surat', 'laporan'])->default('surat');
            $table->timestamps();
        });

        Schema::create('cms_ppid_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kategori');
            $table->date('tanggal');
            $table->string('file_url');
            $table->boolean('aktif')->default(true);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_peta_desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->text('deskripsi')->nullable();
            $table->string('alamat')->nullable();
            $table->boolean('aktif')->default(true);
            $table->string('warna', 7)->default('#dc2626');
            $table->timestamps();
        });

        Schema::create('cms_infografis', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('apbdes_total')->default(0);
            $table->bigInteger('apbdes_realisasi')->default(0);
            $table->decimal('idm_skor', 6, 4)->default(0);
            $table->string('idm_status')->nullable();
            $table->unsignedInteger('stunting_total')->default(0);
            $table->unsignedInteger('stunting_kasus')->default(0);
            $table->json('data_bansos')->nullable();
            $table->json('sdgs_capaian')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_header_footer', function (Blueprint $table) {
            $table->id();
            $table->json('menu_navigasi')->nullable();
            $table->text('teks_footer')->nullable();
            $table->text('kontak_footer')->nullable();
            $table->string('jam_pelayanan')->nullable();
            $table->json('link_sosmed')->nullable();
            $table->string('tombol_wa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_header_footer');
        Schema::dropIfExists('cms_infografis');
        Schema::dropIfExists('cms_peta_desa');
        Schema::dropIfExists('cms_ppid_dokumen');
        Schema::dropIfExists('cms_layanan_publik');
        Schema::dropIfExists('cms_titik_lokasi');
        Schema::dropIfExists('cms_fasilitas');
        Schema::dropIfExists('cms_potensi_desa');
        Schema::dropIfExists('cms_aparatur');
        Schema::dropIfExists('cms_umkm');
        Schema::dropIfExists('cms_galeri');
        Schema::dropIfExists('cms_berita');
        Schema::dropIfExists('cms_profil_desa');
        Schema::dropIfExists('cms_identitas_desa');
    }
};
