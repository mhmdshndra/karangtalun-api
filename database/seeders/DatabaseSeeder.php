<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ═══ 1. USERS ═══════════════════════════════════════════════
        $users = [
            [
                'id' => 1,
                'nama_lengkap' => 'Ahmad Suryanto',
                'nik' => '3314072505850001',
                'no_kk' => '3314070101080001',
                'role' => 'warga',
                'id_petugas' => null,
                'email' => 'ahmad.suryanto@gmail.com',
                'telepon' => '081234567890',
                'alamat' => 'Dukuh Krajan RT 02/RW 01, Desa Karangtalun',
                'rt_rw' => 'RT 02/RW 01',
                'foto' => null,
                'status_aktivasi' => 'aktif',
                'password' => Hash::make('demo123'),
                'tanggal_aktivasi' => '2025-04-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nama_lengkap' => 'Budi Santoso, S.IP.',
                'nik' => '3314071012750001',
                'no_kk' => '3314070201090002',
                'role' => 'admin_desa',
                'id_petugas' => 'ADM-001',
                'email' => 'budi.santoso@karangtalun.desa.id',
                'telepon' => '081298765432',
                'alamat' => 'Dukuh Ngemplak RT 01/RW 03, Desa Karangtalun',
                'rt_rw' => 'RT 01/RW 03',
                'foto' => null,
                'status_aktivasi' => 'aktif',
                'password' => Hash::make('demo123'),
                'tanggal_aktivasi' => '2025-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nama_lengkap' => 'Sari Wulandari, A.Md.',
                'nik' => '3314072203880001',
                'no_kk' => '3314070301100003',
                'role' => 'staf_layanan',
                'id_petugas' => 'STF-001',
                'email' => 'sari.wulandari@karangtalun.desa.id',
                'telepon' => '081356789012',
                'alamat' => 'Dukuh Sidorejo RT 03/RW 02, Desa Karangtalun',
                'rt_rw' => 'RT 03/RW 02',
                'foto' => null,
                'status_aktivasi' => 'aktif',
                'password' => Hash::make('demo123'),
                'tanggal_aktivasi' => '2025-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('users')->insert($users);

        // ═══ 2. KARTU KELUARGA ══════════════════════════════════════
        $kkData = [
            ['no_kk' => '3314070101080001', 'kepala_keluarga' => 'Ahmad Suryanto', 'alamat' => 'Dukuh Krajan', 'rt_rw' => 'RT 02/RW 01', 'kelurahan' => 'Karangtalun', 'kecamatan' => 'Tanon', 'kabupaten' => 'Sragen', 'provinsi' => 'Jawa Tengah', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070201090002', 'kepala_keluarga' => 'Budi Santoso, S.IP.', 'alamat' => 'Dukuh Ngemplak', 'rt_rw' => 'RT 01/RW 03', 'kelurahan' => 'Karangtalun', 'kecamatan' => 'Tanon', 'kabupaten' => 'Sragen', 'provinsi' => 'Jawa Tengah', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070301100003', 'kepala_keluarga' => 'Sari Wulandari, A.Md.', 'alamat' => 'Dukuh Sidorejo', 'rt_rw' => 'RT 03/RW 02', 'kelurahan' => 'Karangtalun', 'kecamatan' => 'Tanon', 'kabupaten' => 'Sragen', 'provinsi' => 'Jawa Tengah', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070401110004', 'kepala_keluarga' => 'Rina Kusuma', 'alamat' => 'Dukuh Sidorejo', 'rt_rw' => 'RT 01/RW 02', 'kelurahan' => 'Karangtalun', 'kecamatan' => 'Tanon', 'kabupaten' => 'Sragen', 'provinsi' => 'Jawa Tengah', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070501120005', 'kepala_keluarga' => 'Teguh Prasetyo', 'alamat' => 'Dukuh Krajan', 'rt_rw' => 'RT 03/RW 01', 'kelurahan' => 'Karangtalun', 'kecamatan' => 'Tanon', 'kabupaten' => 'Sragen', 'provinsi' => 'Jawa Tengah', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('kartu_keluarga')->insert($kkData);

        // ═══ 3. ANGGOTA KK (14 total) ═══════════════════════════════
        $anggota = [
            // KK-1: Ahmad Suryanto (5 anggota)
            ['no_kk' => '3314070101080001', 'nik' => '3314072505850001', 'nama_lengkap' => 'Ahmad Suryanto', 'jenis_kelamin' => 'Laki-laki', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '1985-05-25', 'agama' => 'Islam', 'pendidikan' => 'SMA/Sederajat', 'pekerjaan' => 'Petani', 'status_perkawinan' => 'Kawin', 'status_hubungan' => 'Kepala Keluarga', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070101080001', 'nik' => '3314074112870002', 'nama_lengkap' => 'Siti Nurhaliza', 'jenis_kelamin' => 'Perempuan', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '1987-12-01', 'agama' => 'Islam', 'pendidikan' => 'SMA/Sederajat', 'pekerjaan' => 'Ibu Rumah Tangga', 'status_perkawinan' => 'Kawin', 'status_hubungan' => 'Istri', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070101080001', 'nik' => '3314071506070003', 'nama_lengkap' => 'Rizky Pratama', 'jenis_kelamin' => 'Laki-laki', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '2007-06-15', 'agama' => 'Islam', 'pendidikan' => 'SMA/Sederajat', 'pekerjaan' => 'Pelajar/Mahasiswa', 'status_perkawinan' => 'Belum Kawin', 'status_hubungan' => 'Anak', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070101080001', 'nik' => '3314073008110004', 'nama_lengkap' => 'Ayu Lestari', 'jenis_kelamin' => 'Perempuan', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '2011-08-30', 'agama' => 'Islam', 'pendidikan' => 'SMP/Sederajat', 'pekerjaan' => 'Pelajar/Mahasiswa', 'status_perkawinan' => 'Belum Kawin', 'status_hubungan' => 'Anak', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070101080001', 'nik' => '3314070203130005', 'nama_lengkap' => 'Dimas Aditya', 'jenis_kelamin' => 'Laki-laki', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '2013-03-02', 'agama' => 'Islam', 'pendidikan' => 'SD/Sederajat', 'pekerjaan' => 'Pelajar/Mahasiswa', 'status_perkawinan' => 'Belum Kawin', 'status_hubungan' => 'Anak', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],

            // KK-2: Budi Santoso (3 anggota)
            ['no_kk' => '3314070201090002', 'nik' => '3314071012750001', 'nama_lengkap' => 'Budi Santoso, S.IP.', 'jenis_kelamin' => 'Laki-laki', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '1975-10-12', 'agama' => 'Islam', 'pendidikan' => 'S1', 'pekerjaan' => 'PNS / Kepala Desa', 'status_perkawinan' => 'Kawin', 'status_hubungan' => 'Kepala Keluarga', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070201090002', 'nik' => '3314074506780002', 'nama_lengkap' => 'Ratna Dewi', 'jenis_kelamin' => 'Perempuan', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '1978-06-15', 'agama' => 'Islam', 'pendidikan' => 'S1', 'pekerjaan' => 'Guru', 'status_perkawinan' => 'Kawin', 'status_hubungan' => 'Istri', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070201090002', 'nik' => '3314071203050003', 'nama_lengkap' => 'Fajar Budi Pratama', 'jenis_kelamin' => 'Laki-laki', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '2005-03-12', 'agama' => 'Islam', 'pendidikan' => 'SMA/Sederajat', 'pekerjaan' => 'Pelajar/Mahasiswa', 'status_perkawinan' => 'Belum Kawin', 'status_hubungan' => 'Anak', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],

            // KK-3: Sari Wulandari (1 anggota)
            ['no_kk' => '3314070301100003', 'nik' => '3314072203880001', 'nama_lengkap' => 'Sari Wulandari, A.Md.', 'jenis_kelamin' => 'Perempuan', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '1988-03-22', 'agama' => 'Islam', 'pendidikan' => 'D3', 'pekerjaan' => 'Staf Desa', 'status_perkawinan' => 'Belum Kawin', 'status_hubungan' => 'Kepala Keluarga', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],

            // KK-4: Rina Kusuma (2 anggota)
            ['no_kk' => '3314070401110004', 'nik' => '3314073001900002', 'nama_lengkap' => 'Rina Kusuma', 'jenis_kelamin' => 'Perempuan', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '1990-01-30', 'agama' => 'Islam', 'pendidikan' => 'SMA/Sederajat', 'pekerjaan' => 'Pedagang', 'status_perkawinan' => 'Cerai Hidup', 'status_hubungan' => 'Kepala Keluarga', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070401110004', 'nik' => '3314072105120001', 'nama_lengkap' => 'Nadia Putri Kusuma', 'jenis_kelamin' => 'Perempuan', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '2012-05-21', 'agama' => 'Islam', 'pendidikan' => 'SMP/Sederajat', 'pekerjaan' => 'Pelajar/Mahasiswa', 'status_perkawinan' => 'Belum Kawin', 'status_hubungan' => 'Anak', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],

            // KK-5: Teguh Prasetyo (3 anggota)
            ['no_kk' => '3314070501120005', 'nik' => '3314071506880003', 'nama_lengkap' => 'Teguh Prasetyo', 'jenis_kelamin' => 'Laki-laki', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '1988-06-15', 'agama' => 'Islam', 'pendidikan' => 'SMA/Sederajat', 'pekerjaan' => 'Wiraswasta', 'status_perkawinan' => 'Kawin', 'status_hubungan' => 'Kepala Keluarga', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070501120005', 'nik' => '3314074401900004', 'nama_lengkap' => 'Lina Marlina', 'jenis_kelamin' => 'Perempuan', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '1990-01-14', 'agama' => 'Islam', 'pendidikan' => 'SMA/Sederajat', 'pekerjaan' => 'Ibu Rumah Tangga', 'status_perkawinan' => 'Kawin', 'status_hubungan' => 'Istri', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
            ['no_kk' => '3314070501120005', 'nik' => '3314070708150005', 'nama_lengkap' => 'Andi Prasetyo', 'jenis_kelamin' => 'Laki-laki', 'tempat_lahir' => 'Sragen', 'tanggal_lahir' => '2015-08-07', 'agama' => 'Islam', 'pendidikan' => 'SD/Sederajat', 'pekerjaan' => 'Pelajar/Mahasiswa', 'status_perkawinan' => 'Belum Kawin', 'status_hubungan' => 'Anak', 'kewarganegaraan' => 'WNI', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('anggota_kk')->insert($anggota);

        // ═══ 4. PENGAJUAN SURAT (4 surat) ═══════════════════════════
        $surat = [
            ['id' => 1, 'jenis_surat' => 'surat_keterangan_domisili', 'nomor_tiket' => 'TKT-2025-0001', 'nomor_surat' => '140/35/KT/V/2025', 'status' => 'Selesai', 'tanggal_pengajuan' => '2025-05-10', 'tanggal_diperbarui' => '2025-05-12', 'diajukan_oleh_user_id' => 1, 'diajukan_oleh_nik' => '3314072505850001', 'diajukan_oleh_nama' => 'Ahmad Suryanto', 'pemohon_nik' => '3314072505850001', 'pemohon_nama_lengkap' => 'Ahmad Suryanto', 'pemohon_tempat_lahir' => 'Sragen', 'pemohon_tanggal_lahir' => '1985-05-25', 'pemohon_jenis_kelamin' => 'Laki-laki', 'pemohon_pekerjaan' => 'Petani', 'pemohon_alamat' => 'Dukuh Krajan RT 02/RW 01, Desa Karangtalun, Kec. Tanon, Kab. Sragen', 'pemohon_status_hubungan' => 'Kepala Keluarga', 'keperluan' => 'Untuk keperluan administrasi bank (pengajuan KUR).', 'catatan_admin' => 'Surat telah selesai dan dapat diambil di kantor desa.', 'created_at' => '2025-05-10 08:00:00', 'updated_at' => '2025-05-12 10:00:00'],
            ['id' => 2, 'jenis_surat' => 'surat_keterangan_tidak_mampu', 'nomor_tiket' => 'TKT-2025-0002', 'nomor_surat' => null, 'status' => 'Diproses', 'tanggal_pengajuan' => '2025-05-18', 'tanggal_diperbarui' => '2025-05-19', 'diajukan_oleh_user_id' => 1, 'diajukan_oleh_nik' => '3314072505850001', 'diajukan_oleh_nama' => 'Ahmad Suryanto', 'pemohon_nik' => '3314071506070003', 'pemohon_nama_lengkap' => 'Rizky Pratama', 'pemohon_tempat_lahir' => 'Sragen', 'pemohon_tanggal_lahir' => '2007-06-15', 'pemohon_jenis_kelamin' => 'Laki-laki', 'pemohon_pekerjaan' => 'Pelajar/Mahasiswa', 'pemohon_alamat' => 'Dukuh Krajan RT 02/RW 01, Desa Karangtalun, Kec. Tanon, Kab. Sragen', 'pemohon_status_hubungan' => 'Anak', 'keperluan' => 'Untuk beasiswa pendidikan di SMA Negeri 1 Sragen.', 'catatan_admin' => null, 'created_at' => '2025-05-18 09:00:00', 'updated_at' => '2025-05-19 08:00:00'],
            ['id' => 3, 'jenis_surat' => 'surat_pengantar_skck', 'nomor_tiket' => 'TKT-2025-0003', 'nomor_surat' => '140/42/KT/IV/2025', 'status' => 'Selesai', 'tanggal_pengajuan' => '2025-04-20', 'tanggal_diperbarui' => '2025-04-21', 'diajukan_oleh_user_id' => 1, 'diajukan_oleh_nik' => '3314072505850001', 'diajukan_oleh_nama' => 'Ahmad Suryanto', 'pemohon_nik' => '3314072505850001', 'pemohon_nama_lengkap' => 'Ahmad Suryanto', 'pemohon_tempat_lahir' => 'Sragen', 'pemohon_tanggal_lahir' => '1985-05-25', 'pemohon_jenis_kelamin' => 'Laki-laki', 'pemohon_pekerjaan' => 'Petani', 'pemohon_alamat' => 'Dukuh Krajan RT 02/RW 01, Desa Karangtalun, Kec. Tanon, Kab. Sragen', 'pemohon_status_hubungan' => 'Kepala Keluarga', 'keperluan' => 'Persyaratan melamar pekerjaan.', 'catatan_admin' => 'Surat selesai, silakan ambil di kantor desa jam kerja.', 'created_at' => '2025-04-20 08:00:00', 'updated_at' => '2025-04-21 10:00:00'],
            ['id' => 4, 'jenis_surat' => 'surat_keterangan_usaha', 'nomor_tiket' => 'TKT-2025-0004', 'nomor_surat' => null, 'status' => 'Ditolak', 'tanggal_pengajuan' => '2025-05-22', 'tanggal_diperbarui' => '2025-05-23', 'diajukan_oleh_user_id' => 1, 'diajukan_oleh_nik' => '3314072505850001', 'diajukan_oleh_nama' => 'Ahmad Suryanto', 'pemohon_nik' => '3314074112870002', 'pemohon_nama_lengkap' => 'Siti Nurhaliza', 'pemohon_tempat_lahir' => 'Sragen', 'pemohon_tanggal_lahir' => '1987-12-01', 'pemohon_jenis_kelamin' => 'Perempuan', 'pemohon_pekerjaan' => 'Ibu Rumah Tangga', 'pemohon_alamat' => 'Dukuh Krajan RT 02/RW 01, Desa Karangtalun, Kec. Tanon, Kab. Sragen', 'pemohon_status_hubungan' => 'Istri', 'keperluan' => 'Untuk pengurusan izin usaha warung makan.', 'catatan_admin' => 'Ditolak: Surat pengantar RT/RW belum dilampirkan. Silakan ajukan ulang dengan melampirkan surat pengantar RT/RW.', 'created_at' => '2025-05-22 08:00:00', 'updated_at' => '2025-05-23 14:00:00'],
        ];
        DB::table('pengajuan_surat')->insert($surat);

        // ═══ 5. LAPORAN ADUAN (3 laporan) ═══════════════════════════
        $laporan = [
            ['id' => 1, 'nomor_tiket' => 'LAP-2025-0001', 'kategori' => 'infrastruktur', 'nama_pelapor' => 'Ahmad Suryanto', 'alamat_pelapor' => 'Dukuh Krajan RT 02/RW 01', 'kontak_pelapor' => '081234567890', 'deskripsi' => 'Jalan desa di depan balai desa rusak berat, berlubang dan membahayakan pengendara motor terutama pada malam hari.', 'lokasi_kejadian' => 'Jalan Desa depan Balai Desa Karangtalun', 'lokasi_gps' => null, 'status' => 'Ditindaklanjuti', 'catatan_admin' => 'Sudah dikoordinasikan dengan Dinas PU. Perbaikan dijadwalkan minggu depan.', 'pelapor_user_id' => 1, 'pelapor_nik' => '3314072505850001', 'created_at' => '2025-05-15 10:00:00', 'updated_at' => '2025-05-18 11:30:00'],
            ['id' => 2, 'nomor_tiket' => 'LAP-2025-0002', 'kategori' => 'kamtibmas', 'nama_pelapor' => 'Siti Nurhaliza', 'alamat_pelapor' => 'Dukuh Krajan RT 02/RW 01', 'kontak_pelapor' => '081234567891', 'deskripsi' => 'Lampu penerangan jalan di pertigaan dukuh Sidorejo mati sudah 2 minggu, rawan tindak kriminal.', 'lokasi_kejadian' => 'Pertigaan Dukuh Sidorejo', 'lokasi_gps' => null, 'status' => 'Dikirim', 'catatan_admin' => null, 'pelapor_user_id' => null, 'pelapor_nik' => null, 'created_at' => '2025-05-20 08:00:00', 'updated_at' => '2025-05-20 08:00:00'],
            ['id' => 3, 'nomor_tiket' => 'LAP-2025-0003', 'kategori' => 'umum', 'nama_pelapor' => 'Warga Peduli', 'alamat_pelapor' => 'Dukuh Ngemplak RT 01/RW 03', 'kontak_pelapor' => '081234567892', 'deskripsi' => 'Saluran air di belakang sekolah SD Karangtalun tersumbat sampah sehingga air meluap ke jalan saat hujan deras.', 'lokasi_kejadian' => 'Belakang SD N Karangtalun', 'lokasi_gps' => null, 'status' => 'Selesai', 'catatan_admin' => 'Saluran sudah dibersihkan oleh tim gotong-royong pada 3 Mei 2025. Terima kasih atas laporannya.', 'pelapor_user_id' => null, 'pelapor_nik' => null, 'created_at' => '2025-04-28 08:00:00', 'updated_at' => '2025-05-05 09:00:00'],
        ];
        DB::table('laporan_aduan')->insert($laporan);

        // ═══ 6. PERMOHONAN INFORMASI (3 permohonan) ═════════════════
        $permohonan = [
            ['id' => 1, 'nomor_permohonan' => 'PRM-2025-0001', 'nama_pemohon' => 'Ahmad Suryanto', 'alamat_pemohon' => 'Dukuh Krajan RT 02/RW 01, Desa Karangtalun', 'kontak_pemohon' => '081234567890', 'tujuan_permohonan' => 'Untuk mengetahui alokasi dana desa tahun anggaran 2025 khususnya di bidang pembangunan infrastruktur.', 'informasi_diminta' => 'Rincian APBDes 2025 bidang pembangunan infrastruktur', 'status' => 'Dijawab', 'catatan_admin' => 'Informasi yang diminta bersifat publik dan telah kami sediakan dalam file terlampir.', 'pemohon_user_id' => 1, 'pemohon_nik' => '3314072505850001', 'created_at' => '2025-05-10 08:00:00', 'updated_at' => '2025-05-14 14:00:00'],
            ['id' => 2, 'nomor_permohonan' => 'PRM-2025-0002', 'nama_pemohon' => 'Joko Widodo', 'alamat_pemohon' => 'Dukuh Sidorejo RT 03/RW 02', 'kontak_pemohon' => '081356789012', 'tujuan_permohonan' => 'Penelitian akademik tentang tata kelola desa digital.', 'informasi_diminta' => 'Data jumlah layanan digital desa dan statistik penggunaan e-surat tahun 2024-2025', 'status' => 'Diproses', 'catatan_admin' => 'Sedang dikompilasi oleh operator desa.', 'pemohon_user_id' => null, 'pemohon_nik' => null, 'created_at' => '2025-05-20 08:00:00', 'updated_at' => '2025-05-21 08:00:00'],
            ['id' => 3, 'nomor_permohonan' => 'PRM-2025-0003', 'nama_pemohon' => 'Wartawan Lokal', 'alamat_pemohon' => 'Kota Sragen', 'kontak_pemohon' => '081298765432', 'tujuan_permohonan' => 'Liputan media tentang program desa digital Karangtalun.', 'informasi_diminta' => 'Profil program digitalisasi desa, capaian, dan rencana pengembangan', 'status' => 'Dikirim', 'catatan_admin' => null, 'pemohon_user_id' => null, 'pemohon_nik' => null, 'created_at' => '2025-05-22 08:00:00', 'updated_at' => '2025-05-22 08:00:00'],
        ];
        DB::table('permohonan_informasi')->insert($permohonan);

        // ═══ 7. NOTIFIKASI (7 + 3 dari laporanMock) ═════════════════
        $notifikasi = [
            ['tipe' => 'surat_selesai', 'judul' => 'Surat Domisili Selesai', 'pesan' => 'Surat Keterangan Domisili (TKT-2025-0001) telah selesai diproses. Silakan ambil di kantor desa pada jam kerja.', 'tanggal' => '2025-05-12 10:30:00', 'dibaca' => true, 'link' => '/warga/riwayat', 'target_role' => 'warga', 'target_user_id' => 1, 'target_nik' => '3314072505850001', 'created_at' => '2025-05-12 10:30:00', 'updated_at' => '2025-05-12 10:30:00'],
            ['tipe' => 'surat_diproses', 'judul' => 'SKTM Sedang Diproses', 'pesan' => 'Pengajuan SKTM (TKT-2025-0002) atas nama Rizky Pratama sedang diproses oleh staf desa.', 'tanggal' => '2025-05-19 08:15:00', 'dibaca' => false, 'link' => '/warga/riwayat', 'target_role' => 'warga', 'target_user_id' => 1, 'target_nik' => '3314072505850001', 'created_at' => '2025-05-19 08:15:00', 'updated_at' => '2025-05-19 08:15:00'],
            ['tipe' => 'surat_ditolak', 'judul' => 'Surat Keterangan Usaha Ditolak', 'pesan' => 'Pengajuan Surat Keterangan Usaha (TKT-2025-0004) ditolak karena berkas tidak lengkap. Silakan ajukan ulang.', 'tanggal' => '2025-05-23 14:00:00', 'dibaca' => false, 'link' => '/warga/riwayat', 'target_role' => 'warga', 'target_user_id' => 1, 'target_nik' => '3314072505850001', 'created_at' => '2025-05-23 14:00:00', 'updated_at' => '2025-05-23 14:00:00'],
            ['tipe' => 'pengumuman', 'judul' => 'Jadwal Pelayanan Hari Raya', 'pesan' => 'Pelayanan administrasi desa libur tanggal 29-31 Mei 2025. Pelayanan kembali normal tanggal 2 Juni 2025.', 'tanggal' => '2025-05-25 07:00:00', 'dibaca' => false, 'link' => null, 'target_role' => 'all', 'target_user_id' => null, 'target_nik' => null, 'created_at' => '2025-05-25 07:00:00', 'updated_at' => '2025-05-25 07:00:00'],
            ['tipe' => 'surat_masuk', 'judul' => 'Pengajuan Surat Baru Masuk', 'pesan' => 'Pengajuan SKTM (TKT-2025-0002) atas nama Rizky Pratama memerlukan verifikasi.', 'tanggal' => '2025-05-18 09:00:00', 'dibaca' => false, 'link' => '/staf/pengajuan-surat', 'target_role' => 'staf_layanan', 'target_user_id' => null, 'target_nik' => null, 'created_at' => '2025-05-18 09:00:00', 'updated_at' => '2025-05-18 09:00:00'],
            ['tipe' => 'surat_masuk', 'judul' => 'Pengajuan Surat Keterangan Usaha', 'pesan' => 'Pengajuan Surat Keterangan Usaha (TKT-2025-0004) baru masuk dari warga atas nama Siti Nurhaliza.', 'tanggal' => '2025-05-22 08:30:00', 'dibaca' => false, 'link' => '/admin/pengajuan-surat', 'target_role' => 'admin_desa', 'target_user_id' => null, 'target_nik' => null, 'created_at' => '2025-05-22 08:30:00', 'updated_at' => '2025-05-22 08:30:00'],
            ['tipe' => 'laporan_masuk', 'judul' => 'Laporan Baru: Kerusakan Jalan', 'pesan' => 'Laporan kerusakan jalan desa (LAP-2025-0001) baru masuk dan memerlukan tindak lanjut.', 'tanggal' => '2025-05-15 10:00:00', 'dibaca' => false, 'link' => '/admin/laporan-aduan', 'target_role' => 'admin_desa', 'target_user_id' => null, 'target_nik' => null, 'created_at' => '2025-05-15 10:00:00', 'updated_at' => '2025-05-15 10:00:00'],
            // Notifikasi from laporanMock
            ['tipe' => 'laporan_selesai', 'judul' => 'Laporan Selesai Ditindaklanjuti', 'pesan' => 'Laporan saluran air tersumbat (LAP-2025-0003) telah diselesaikan. Terima kasih atas partisipasi Anda.', 'tanggal' => '2025-05-05 09:00:00', 'dibaca' => true, 'link' => '/layanan/laporan/lap-003', 'target_role' => 'all', 'target_user_id' => null, 'target_nik' => null, 'created_at' => '2025-05-05 09:00:00', 'updated_at' => '2025-05-05 09:00:00'],
            ['tipe' => 'laporan_diproses', 'judul' => 'Laporan Jalan Rusak Ditindaklanjuti', 'pesan' => 'Laporan kerusakan jalan (LAP-2025-0001) sedang dikoordinasikan dengan Dinas PU. Perbaikan dijadwalkan minggu depan.', 'tanggal' => '2025-05-18 11:30:00', 'dibaca' => false, 'link' => '/layanan/laporan/lap-001', 'target_role' => 'all', 'target_user_id' => null, 'target_nik' => null, 'created_at' => '2025-05-18 11:30:00', 'updated_at' => '2025-05-18 11:30:00'],
            ['tipe' => 'permohonan_selesai', 'judul' => 'Permohonan Informasi Dijawab', 'pesan' => 'Permohonan informasi APBDes (PRM-2025-0001) telah dijawab. File balasan sudah tersedia.', 'tanggal' => '2025-05-14 14:00:00', 'dibaca' => false, 'link' => '/layanan/permohonan/prm-001', 'target_role' => 'all', 'target_user_id' => null, 'target_nik' => null, 'created_at' => '2025-05-14 14:00:00', 'updated_at' => '2025-05-14 14:00:00'],
        ];
        DB::table('notifikasi')->insert($notifikasi);

        // ═══ 8. CMS: IDENTITAS DESA ═════════════════════════════════
        DB::table('cms_identitas_desa')->insert([
            'nama_desa' => 'Karangtalun', 'kode_desa' => '33.14.07.2005', 'kecamatan' => 'Tanon', 'kabupaten' => 'Sragen', 'provinsi' => 'Jawa Tengah', 'kode_pos' => '57277',
            'alamat' => 'Jl. Raya Karangtalun No. 1, Dusun I, Kec. Tanon, Kab. Sragen, Jawa Tengah 57277',
            'email' => 'pemdes@karangtalun.desa.id', 'telepon' => '0812-3456-7890',
            'maps_url' => 'https://maps.google.com/?q=Karangtalun,Tanon,Sragen,Jawa+Tengah',
            'koordinat_lat' => -7.3574, 'koordinat_lng' => 111.0089,
            'nama_kades' => 'Budi Santoso, S.IP.', 'jabatan_kades' => 'Kepala Desa Karangtalun', 'tahun_anggaran' => '2026',
            'sosmed_facebook' => 'https://facebook.com/desakarangtalun', 'sosmed_instagram' => 'https://instagram.com/desakarangtalun',
            'sosmed_twitter' => '', 'sosmed_youtube' => '', 'sosmed_tiktok' => '',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ═══ 9. CMS: PROFIL DESA ════════════════════════════════════
        DB::table('cms_profil_desa')->insert([
            'sejarah' => 'Desa Karangtalun merupakan desa yang terletak di Kecamatan Tanon, Kabupaten Sragen, Jawa Tengah. Desa ini berdiri sejak tahun 1948 dan memiliki sejarah panjang dalam perjuangan kemerdekaan Indonesia. Nama Karangtalun berasal dari kata \'karang\' yang berarti batu dan \'talun\' yang berarti kebun, menggambarkan kondisi geografis desa yang berupa dataran dengan lahan pertanian yang subur.',
            'visi' => 'Terwujudnya Desa Karangtalun yang Mandiri, Maju, Sejahtera, dan Berbudaya.',
            'misi' => json_encode(['Meningkatkan kualitas pelayanan publik yang prima dan transparan', 'Mengembangkan potensi ekonomi desa melalui BUMDes dan UMKM', 'Meningkatkan kualitas sumber daya manusia melalui pendidikan dan kesehatan', 'Melestarikan nilai-nilai budaya dan kearifan lokal', 'Membangun infrastruktur desa yang memadai dan berkelanjutan']),
            'potensi' => 'Desa Karangtalun memiliki potensi pertanian yang luas, terutama padi, palawija, dan perkebunan. Selain itu, desa ini juga memiliki potensi UMKM yang berkembang pesat, terutama dalam bidang kuliner, kerajinan batik tulis, dan produk olahan pertanian.',
            'sambutan' => "Assalamualaikum Wr. Wb.\n\nSelamat datang di Portal Resmi Desa Karangtalun. Melalui portal ini, kami berupaya memberikan pelayanan informasi dan administrasi yang mudah, cepat, dan transparan bagi seluruh warga desa. Semoga portal ini dapat menjadi jembatan komunikasi yang baik antara pemerintah desa dan masyarakat.\n\nWassalamualaikum Wr. Wb.\n\nKepala Desa Karangtalun,\nBudi Santoso, S.IP.",
            'foto_kades' => '/assets/officials/kades.jpg',
            'struktur_pemerintahan' => 'Pemerintah Desa Karangtalun dipimpin oleh Kepala Desa yang dibantu oleh Sekretaris Desa, Kepala Urusan (Kaur), Kepala Seksi (Kasi), dan Kepala Dusun (Kadus). Struktur ini sesuai dengan Peraturan Pemerintah tentang Organisasi Pemerintah Desa.',
            'fasilitas_teks' => 'Balai Desa, Puskesmas Pembantu, SDN 1 & SDN 2 Karangtalun, Masjid Al-Ikhlas, Mushola Al-Hidayah, Pasar Desa, Lapangan Olahraga, Taman Baca Masyarakat.',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ═══ 10. CMS: BERITA (6 berita) ═════════════════════════════
        $berita = [
            ['judul' => 'Penyaluran BLT Dana Desa Tahap 3 Tahun 2026 Berjalan Lancar', 'slug' => 'penyaluran-blt-dana-desa-tahap-3', 'kategori' => 'Pemberdayaan', 'penulis' => 'Admin Utama', 'tanggal' => '2026-05-04', 'waktu' => '09:15 WIB', 'views' => 1250, 'status' => 'Terbit', 'tipe' => 'Artikel', 'link_video' => null, 'thumbnail' => '/assets/news/berita-1.jpg', 'konten' => 'Pemerintah Desa Karangtalun telah sukses menyalurkan Bantuan Langsung Tunai (BLT) Dana Desa kepada Keluarga Penerima Manfaat (KPM) tahap ketiga tahun 2026. Penyaluran dilaksanakan secara langsung di Balai Desa Karangtalun dengan didampingi perangkat desa dan pendamping desa. Sebanyak 87 KPM menerima bantuan senilai Rp 300.000 per bulan.', 'is_featured' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Kerja Bakti Massal Persiapan Lomba Desa Tingkat Kabupaten', 'slug' => 'kerja-bakti-massal-persiapan-lomba-desa', 'kategori' => 'Pembinaan', 'penulis' => 'Staf Layanan', 'tanggal' => '2026-05-02', 'waktu' => '14:30 WIB', 'views' => 840, 'status' => 'Terbit', 'tipe' => 'Video', 'link_video' => null, 'thumbnail' => '/assets/news/berita-2.jpg', 'konten' => 'Saksikan antusiasme warga Desa Karangtalun dalam mempersiapkan lingkungan desa untuk lomba desa tingkat kabupaten. Ratusan warga dari berbagai dusun bergotong royong membersihkan dan memperindah lingkungan sekitar.', 'is_featured' => false, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Peresmian Jalan Usaha Tani Dusun Sukosari oleh Bupati Sragen', 'slug' => 'peresmian-jalan-usaha-tani-dusun-sukosari', 'kategori' => 'Pembangunan', 'penulis' => 'Staf Layanan', 'tanggal' => '2026-04-28', 'waktu' => '16:20 WIB', 'views' => 2100, 'status' => 'Terbit', 'tipe' => 'Video', 'link_video' => 'https://youtube.com/watch?v=dQw4w9WgXcQ', 'thumbnail' => '/assets/news/berita-3.jpg', 'konten' => 'Bupati Sragen secara langsung meresmikan jalan usaha tani sepanjang 1,2 km yang menghubungkan blok barat dan timur Dusun Sukosari.', 'is_featured' => false, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Peringatan Hari Jadi Desa Karangtalun Ke-78 Berlangsung Meriah', 'slug' => 'hari-jadi-desa-karangtalun-ke-78', 'kategori' => 'Pengumuman', 'penulis' => 'Admin Utama', 'tanggal' => '2026-04-15', 'waktu' => '08:45 WIB', 'views' => 3450, 'status' => 'Terbit', 'tipe' => 'Artikel', 'link_video' => null, 'thumbnail' => '/assets/news/berita-4.jpg', 'konten' => 'Rangkaian acara peringatan hari jadi Desa Karangtalun ke-78 berlangsung meriah selama sepekan penuh, mulai tanggal 10 hingga 15 April 2026.', 'is_featured' => false, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Pelatihan Digital Marketing untuk Pelaku UMKM Desa', 'slug' => 'pelatihan-digital-marketing-umkm', 'kategori' => 'Pemberdayaan', 'penulis' => 'Admin Utama', 'tanggal' => '2026-04-10', 'waktu' => '11:10 WIB', 'views' => 695, 'status' => 'Terbit', 'tipe' => 'Artikel', 'link_video' => null, 'thumbnail' => '/assets/news/berita-5.jpg', 'konten' => 'Pemerintah Desa Karangtalun bekerja sama dengan Dinas Koperasi dan UMKM Kabupaten Sragen menyelenggarakan pelatihan digital marketing bagi 40 pelaku UMKM desa.', 'is_featured' => false, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Musdes Penetapan Prioritas Penggunaan Dana Desa Tahun 2026', 'slug' => 'musdes-penetapan-prioritas-penggunaan-dana-desa-2026', 'kategori' => 'Pemerintahan', 'penulis' => 'Admin Utama', 'tanggal' => '2026-04-05', 'waktu' => '13:00 WIB', 'views' => 512, 'status' => 'Terbit', 'tipe' => 'Artikel', 'link_video' => null, 'thumbnail' => '/assets/news/berita-6.jpg', 'konten' => 'Musyawarah Desa (Musdes) penetapan prioritas penggunaan Dana Desa tahun 2026 telah dilaksanakan di Balai Desa Karangtalun.', 'is_featured' => false, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('cms_berita')->insert($berita);

        // ═══ 11. CMS: GALERI (6) ════════════════════════════════════
        $galeri = [
            ['judul' => 'Penyaluran BLT Dana Desa 2026', 'url' => '/assets/gallery/galeri-1.jpg', 'kategori' => 'Kegiatan', 'tanggal' => '2026-05-04', 'deskripsi' => '', 'urutan' => 1, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Kerja Bakti Serentak', 'url' => '/assets/gallery/galeri-2.jpg', 'kategori' => 'Gotong Royong', 'tanggal' => '2026-05-02', 'deskripsi' => '', 'urutan' => 2, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Peresmian Jalan Usaha Tani', 'url' => '/assets/gallery/galeri-3.jpg', 'kategori' => 'Pembangunan', 'tanggal' => '2026-04-28', 'deskripsi' => '', 'urutan' => 3, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Hari Jadi Desa Ke-78', 'url' => '/assets/gallery/galeri-4.jpg', 'kategori' => 'Acara', 'tanggal' => '2026-04-15', 'deskripsi' => '', 'urutan' => 4, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Pameran UMKM Desa', 'url' => '/assets/gallery/galeri-5.jpg', 'kategori' => 'Ekonomi', 'tanggal' => '2026-04-10', 'deskripsi' => '', 'urutan' => 5, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Musyawarah Desa 2026', 'url' => '/assets/gallery/galeri-6.jpg', 'kategori' => 'Pemerintahan', 'tanggal' => '2026-04-05', 'deskripsi' => '', 'urutan' => 6, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('cms_galeri')->insert($galeri);

        // ═══ 12. CMS: UMKM (6 produk) ══════════════════════════════
        $umkm = [
            ['nama' => 'Keripik Singkong Pedas Manis Balado (250gr)', 'slug' => 'kripik-singkong-balado', 'kategori' => 'Sembako & Camilan', 'nama_penjual' => 'Ibu Siti Fatimah', 'rt_rw' => 'RT 001 / RW 002', 'whatsapp' => '6281234567890', 'harga' => 15000, 'foto' => '/assets/products/kripik-1.jpg', 'deskripsi' => 'Keripik singkong balado khas desa, renyah dan pedas manis.', 'likes' => 124, 'aktif' => true, 'unggulan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Konektor Masker Rajut Tangan Berbagai Warna', 'slug' => 'konektor-masker-rajut', 'kategori' => 'Aksesoris & Kerajinan', 'nama_penjual' => 'Karya Remaja RT 03', 'rt_rw' => 'RT 003 / RW 001', 'whatsapp' => '6289876543210', 'harga' => 10000, 'foto' => '/assets/products/rajut-1.jpg', 'deskripsi' => 'Konektor masker rajut buatan tangan.', 'likes' => 89, 'aktif' => true, 'unggulan' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Kue Talam Susu Khas Hajatan (Per Mika isi 5)', 'slug' => 'kue-talam-susu', 'kategori' => 'Sembako & Camilan', 'nama_penjual' => 'Dapur Bu Ningsih', 'rt_rw' => 'RT 002 / RW 001', 'whatsapp' => '6285512345678', 'harga' => 12000, 'foto' => '/assets/products/kue-1.jpg', 'deskripsi' => 'Kue talam susu lembut dan gurih.', 'likes' => 210, 'aktif' => true, 'unggulan' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Batik Tulis Motif Padi Karangtalun (Kain 2m)', 'slug' => 'batik-tulis-motif-padi', 'kategori' => 'Kerajinan Tangan', 'nama_penjual' => 'Kelompok Batik Srikandi', 'rt_rw' => 'RT 004 / RW 002', 'whatsapp' => '6287712345678', 'harga' => 185000, 'foto' => '/assets/products/batik-1.jpg', 'deskripsi' => 'Batik tulis eksklusif bermotif padi.', 'likes' => 43, 'aktif' => true, 'unggulan' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Minyak Kelapa Murni VCO (500ml)', 'slug' => 'minyak-kelapa-murni', 'kategori' => 'Sembako & Camilan', 'nama_penjual' => 'BUMDes Karangtalun Makmur', 'rt_rw' => 'Produksi BUMDes', 'whatsapp' => '6281198765432', 'harga' => 45000, 'foto' => '/assets/products/vco-1.jpg', 'deskripsi' => 'VCO murni diproses dingin dari kelapa segar.', 'likes' => 167, 'aktif' => true, 'unggulan' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pupuk Organik Kompos (Karung 25kg)', 'slug' => 'pupuk-organik-kompos', 'kategori' => 'Pertanian', 'nama_penjual' => 'Poktan Tani Makmur', 'rt_rw' => 'RT 005 / RW 003', 'whatsapp' => '6285599887766', 'harga' => 35000, 'foto' => '/assets/products/pupuk-1.jpg', 'deskripsi' => 'Pupuk organik kompos berkualitas tinggi.', 'likes' => 98, 'aktif' => true, 'unggulan' => false, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('cms_umkm')->insert($umkm);

        // ═══ 13. CMS: APARATUR (10) ═════════════════════════════════
        $aparatur = [
            ['nama' => 'Budi Santoso, S.IP.', 'jabatan' => 'Kepala Desa', 'foto' => '/assets/officials/kades.jpg', 'kategori_jabatan' => 'Pimpinan', 'urutan' => 1, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Hadi Purnomo', 'jabatan' => 'Sekretaris Desa', 'foto' => null, 'kategori_jabatan' => 'Pimpinan', 'urutan' => 2, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Sri Rahayu', 'jabatan' => 'Kaur Keuangan', 'foto' => null, 'kategori_jabatan' => 'Kaur', 'urutan' => 3, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Agus Widodo', 'jabatan' => 'Kaur Perencanaan', 'foto' => null, 'kategori_jabatan' => 'Kaur', 'urutan' => 4, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Bambang Setiawan', 'jabatan' => 'Kasi Pemerintahan', 'foto' => null, 'kategori_jabatan' => 'Kasi', 'urutan' => 5, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Sari Dewi', 'jabatan' => 'Kasi Pelayanan', 'foto' => null, 'kategori_jabatan' => 'Kasi', 'urutan' => 6, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Dwi Cahyono', 'jabatan' => 'Kasi Kesejahteraan', 'foto' => null, 'kategori_jabatan' => 'Kasi', 'urutan' => 7, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Eko Susanto', 'jabatan' => 'Kepala Dusun I', 'foto' => null, 'kategori_jabatan' => 'Kadus', 'urutan' => 8, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Mulyadi', 'jabatan' => 'Kepala Dusun II', 'foto' => null, 'kategori_jabatan' => 'Kadus', 'urutan' => 9, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Tino Wibowo', 'jabatan' => 'Kepala Dusun III', 'foto' => null, 'kategori_jabatan' => 'Kadus', 'urutan' => 10, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('cms_aparatur')->insert($aparatur);

        // ═══ 14. CMS: POTENSI DESA (6) ══════════════════════════════
        $potensi = [
            ['judul' => 'Pertanian', 'deskripsi' => 'Lahan sawah seluas ±180 Ha menghasilkan padi dan palawija dengan produktivitas tinggi.', 'gambar' => '/assets/backgrounds/desa-1.jpg', 'urutan' => 1, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'UMKM', 'deskripsi' => '42 unit usaha mikro aktif bergerak di bidang kuliner, kerajinan, dan perdagangan.', 'gambar' => '/assets/backgrounds/desa-2.jpg', 'urutan' => 2, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Wisata Desa', 'deskripsi' => 'Potensi wisata alam dan budaya yang siap dikembangkan sebagai destinasi unggulan.', 'gambar' => '/assets/backgrounds/desa-3.jpg', 'urutan' => 3, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'BUMDes', 'deskripsi' => 'BUMDes Karangtalun Makmur aktif mengelola unit usaha simpan pinjam dan pasar desa.', 'gambar' => null, 'urutan' => 4, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Lokasi Strategis', 'deskripsi' => 'Terletak di jalur utama Sragen-Ngawi dengan akses transportasi yang mudah.', 'gambar' => null, 'urutan' => 5, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Digital Ready', 'deskripsi' => 'Seluruh layanan administrasi dapat diakses secara digital melalui portal ini.', 'gambar' => null, 'urutan' => 6, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('cms_potensi_desa')->insert($potensi);

        // ═══ 15. CMS: FASILITAS + TITIK LOKASI ═════════════════════
        $fasilitas = [
            ['id' => 1, 'nama' => 'Balai Desa', 'deskripsi' => 'Pusat administrasi dan pelayanan masyarakat', 'gambar' => '/assets/facilities/balai-desa.jpg', 'label' => '1 unit', 'urutan' => 1, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama' => 'Puskesmas Pembantu', 'deskripsi' => 'Pelayanan kesehatan dasar dan posyandu', 'gambar' => '/assets/facilities/puskesmas.jpg', 'label' => '1 unit', 'urutan' => 2, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama' => 'Sekolah Dasar', 'deskripsi' => 'SDN 1 & SDN 2 Karangtalun', 'gambar' => '/assets/facilities/sekolah.jpg', 'label' => '2 unit', 'urutan' => 3, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama' => 'Masjid & Mushola', 'deskripsi' => 'Tempat ibadah umat Islam', 'gambar' => '/assets/facilities/masjid.jpg', 'label' => '2 unit', 'urutan' => 4, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama' => 'Pasar Desa', 'deskripsi' => 'Pusat perdagangan dan ekonomi lokal', 'gambar' => '/assets/facilities/pasar.jpg', 'label' => '1 unit', 'urutan' => 5, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nama' => 'Jalan Desa', 'deskripsi' => 'Jalan usaha tani dan jalan desa yang sudah beraspal', 'gambar' => '/assets/facilities/jalan.jpg', 'label' => '±12 km', 'urutan' => 6, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('cms_fasilitas')->insert($fasilitas);

        $titikLokasi = [
            ['cms_fasilitas_id' => 1, 'nama' => 'Balai Desa Karangtalun', 'label' => 'Jl. Raya Karangtalun No. 1', 'lat' => -7.3574, 'lng' => 111.0089, 'route_link' => 'https://maps.google.com/?q=Karangtalun,Tanon,Sragen', 'urutan' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['cms_fasilitas_id' => 2, 'nama' => 'Pustu Karangtalun', 'label' => 'Kec. Tanon, Sragen', 'lat' => -7.3560, 'lng' => 111.0102, 'route_link' => 'https://maps.google.com/?q=Karangtalun,Tanon,Sragen', 'urutan' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['cms_fasilitas_id' => 3, 'nama' => 'SDN 1 Karangtalun', 'label' => 'Dusun I', 'lat' => -7.3590, 'lng' => 111.0075, 'route_link' => 'https://maps.google.com/?q=SDN+1+Karangtalun,Sragen', 'urutan' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['cms_fasilitas_id' => 3, 'nama' => 'SDN 2 Karangtalun', 'label' => 'Dusun II', 'lat' => -7.3585, 'lng' => 111.0095, 'route_link' => 'https://maps.google.com/?q=SDN+2+Karangtalun,Sragen', 'urutan' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['cms_fasilitas_id' => 4, 'nama' => 'Masjid Al-Ikhlas', 'label' => 'Dusun I', 'lat' => -7.3568, 'lng' => 111.0093, 'route_link' => 'https://maps.google.com/?q=Karangtalun,Tanon,Sragen', 'urutan' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['cms_fasilitas_id' => 4, 'nama' => 'Mushola An-Nur', 'label' => 'Dusun II', 'lat' => -7.3570, 'lng' => 111.0100, 'route_link' => '', 'urutan' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['cms_fasilitas_id' => 5, 'nama' => 'Pasar Desa Karangtalun', 'label' => 'Jl. Raya Karangtalun', 'lat' => -7.3580, 'lng' => 111.0065, 'route_link' => 'https://maps.google.com/?q=Karangtalun,Tanon,Sragen', 'urutan' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('cms_titik_lokasi')->insert($titikLokasi);

        // ═══ 16. CMS: LAYANAN PUBLIK (7 layanan) ═══════════════════
        $layanan = [
            ['nama' => 'Surat Keterangan Domisili', 'deskripsi' => 'Surat keterangan yang menyatakan bahwa seseorang bertempat tinggal di wilayah Desa Karangtalun.', 'kategori' => 'kependudukan', 'estimasi_waktu' => '1 Hari Kerja', 'biaya' => 'Gratis', 'persyaratan' => json_encode(['Fotokopi KTP', 'Fotokopi KK', 'Surat pengantar RT/RW', 'Pas foto 3x4 (2 lembar)']), 'prosedur' => json_encode(['Datang ke kantor desa atau ajukan online', 'Isi formulir permohonan', 'Lampirkan dokumen persyaratan', 'Tunggu verifikasi (1 hari kerja)', 'Ambil surat di kantor desa']), 'aktif' => true, 'butuh_login' => true, 'instruksi' => 'Silakan datang ke kantor desa pada jam kerja atau ajukan secara online melalui menu e-Surat.', 'route_slug' => 'suket-domisili', 'tipe_layanan' => 'surat', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Surat Keterangan Tidak Mampu (SKTM)', 'deskripsi' => 'Surat keterangan yang menyatakan bahwa seseorang atau keluarga termasuk kategori tidak mampu.', 'kategori' => 'sosial', 'estimasi_waktu' => '2 Hari Kerja', 'biaya' => 'Gratis', 'persyaratan' => json_encode(['Fotokopi KTP', 'Fotokopi KK', 'Surat pengantar RT/RW', 'Keterangan penghasilan']), 'prosedur' => json_encode(['Datang ke kantor desa atau ajukan online', 'Isi formulir permohonan', 'Lampirkan dokumen persyaratan', 'Verifikasi lapangan', 'Surat diterbitkan dalam 2 hari kerja']), 'aktif' => true, 'butuh_login' => true, 'instruksi' => 'Pastikan semua dokumen sudah lengkap sebelum mengajukan.', 'route_slug' => 'sktm', 'tipe_layanan' => 'surat', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Surat Pengantar SKCK', 'deskripsi' => 'Surat pengantar dari desa untuk mengajukan SKCK ke Kepolisian.', 'kategori' => 'kependudukan', 'estimasi_waktu' => '1 Hari Kerja', 'biaya' => 'Gratis', 'persyaratan' => json_encode(['Fotokopi KTP', 'Fotokopi KK', 'Surat pengantar RT/RW', 'Pas foto 4x6 latar merah (4 lembar)']), 'prosedur' => json_encode(['Ajukan permohonan', 'Isi formulir', 'Lampirkan dokumen', 'Tunggu verifikasi', 'Ambil surat pengantar']), 'aktif' => true, 'butuh_login' => true, 'instruksi' => 'Surat pengantar ini diperlukan untuk mengurus SKCK di Polsek setempat.', 'route_slug' => 'pengantar-skck', 'tipe_layanan' => 'surat', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Surat Keterangan Usaha', 'deskripsi' => 'Surat keterangan bahwa seseorang memiliki usaha di wilayah desa.', 'kategori' => 'ekonomi', 'estimasi_waktu' => '2 Hari Kerja', 'biaya' => 'Gratis', 'persyaratan' => json_encode(['Fotokopi KTP', 'Fotokopi KK', 'Surat pengantar RT/RW', 'Foto lokasi usaha']), 'prosedur' => json_encode(['Ajukan permohonan', 'Isi formulir dan deskripsi usaha', 'Lampirkan dokumen', 'Verifikasi lokasi usaha', 'Surat diterbitkan']), 'aktif' => true, 'butuh_login' => true, 'instruksi' => 'Siapkan foto lokasi usaha minimal 2 foto.', 'route_slug' => 'suket-usaha', 'tipe_layanan' => 'surat', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lapor Kerusakan Infrastruktur', 'deskripsi' => 'Laporkan kerusakan jalan, jembatan, fasilitas umum, saluran air, atau infrastruktur desa lainnya.', 'kategori' => 'infrastruktur', 'estimasi_waktu' => '3-7 Hari Kerja', 'biaya' => 'Gratis', 'persyaratan' => json_encode(['Identitas pelapor (KTP)', 'Foto kerusakan infrastruktur', 'Lokasi kerusakan yang jelas', 'Deskripsi kerusakan']), 'prosedur' => json_encode(['Isi formulir laporan secara online atau di kantor desa', 'Unggah foto kerusakan', 'Cantumkan lokasi dengan pin GPS atau alamat lengkap', 'Laporan diterima dan diverifikasi petugas', 'Jadwal perbaikan akan dikonfirmasi']), 'aktif' => true, 'butuh_login' => false, 'instruksi' => 'Sertakan foto dan lokasi agar pelaporan lebih cepat ditindaklanjuti.', 'route_slug' => 'lapor-infrastruktur', 'tipe_layanan' => 'laporan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lapor Gangguan Kamtibmas', 'deskripsi' => 'Laporkan gangguan ketertiban, keamanan, atau kejadian luar biasa di lingkungan Desa Karangtalun.', 'kategori' => 'keamanan', 'estimasi_waktu' => '1x24 Jam', 'biaya' => 'Gratis', 'persyaratan' => json_encode(['Identitas pelapor (KTP)', 'Kronologi kejadian', 'Foto/video pendukung (jika ada)', 'Lokasi kejadian']), 'prosedur' => json_encode(['Isi formulir laporan secara online atau hubungi hotline desa', 'Deskripsikan kejadian secara lengkap', 'Laporan diteruskan ke Babinsa/Bhabinkamtibmas', 'Tindak lanjut dilakukan dalam 1x24 jam', 'Pelapor mendapatkan nomor tiket laporan']), 'aktif' => true, 'butuh_login' => false, 'instruksi' => 'Untuk keadaan darurat, hubungi langsung nomor darurat desa.', 'route_slug' => 'lapor-kamtibmas', 'tipe_layanan' => 'laporan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lapor Umum', 'deskripsi' => 'Sampaikan keluhan, saran, atau laporan umum terkait pelayanan desa, lingkungan, sosial, atau permasalahan lainnya.', 'kategori' => 'umum', 'estimasi_waktu' => '3-5 Hari Kerja', 'biaya' => 'Gratis', 'persyaratan' => json_encode(['Identitas pelapor', 'Deskripsi permasalahan / saran', 'Lampiran pendukung (opsional)']), 'prosedur' => json_encode(['Isi formulir laporan secara online', 'Deskripsikan permasalahan atau saran secara lengkap', 'Laporan diteruskan ke perangkat desa terkait', 'Tindak lanjut disampaikan melalui kontak yang diberikan', 'Pelapor mendapatkan nomor tiket laporan']), 'aktif' => true, 'butuh_login' => false, 'instruksi' => 'Laporan Anda akan dijaga kerahasiaannya.', 'route_slug' => 'lapor-umum', 'tipe_layanan' => 'laporan', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('cms_layanan_publik')->insert($layanan);

        // ═══ 17. CMS: PPID DOKUMEN (6) ══════════════════════════════
        $ppid = [
            ['judul' => 'Laporan Realisasi APBDes Tahun 2025', 'kategori' => 'Informasi Berkala', 'tanggal' => '2025-12-31', 'file_url' => '#', 'aktif' => true, 'urutan' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Laporan Penyelenggaraan Pemerintahan Desa (LPPD) Tahun 2025', 'kategori' => 'Informasi Berkala', 'tanggal' => '2026-01-15', 'file_url' => '#', 'aktif' => true, 'urutan' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'RKP Desa Tahun 2026', 'kategori' => 'Informasi Berkala', 'tanggal' => '2025-11-15', 'file_url' => '#', 'aktif' => true, 'urutan' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Pengumuman Tanggap Darurat Banjir', 'kategori' => 'Informasi Serta Merta', 'tanggal' => '2026-03-20', 'file_url' => '#', 'aktif' => true, 'urutan' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Profil Desa Karangtalun Tahun 2026', 'kategori' => 'Informasi Setiap Saat', 'tanggal' => '2026-01-01', 'file_url' => '#', 'aktif' => true, 'urutan' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'UU No 14 Tahun 2008 tentang KIP', 'kategori' => 'Dasar Hukum', 'tanggal' => '2008-04-30', 'file_url' => '#', 'aktif' => true, 'urutan' => 6, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('cms_ppid_dokumen')->insert($ppid);

        // ═══ 18. CMS: PETA DESA (5 marker) ═════════════════════════
        $peta = [
            ['nama' => 'Balai Desa Karangtalun', 'kategori' => 'fasilitas', 'lat' => -7.3574, 'lng' => 111.0089, 'deskripsi' => 'Pusat Pelayanan Administrasi Desa', 'alamat' => 'Jl. Raya Karangtalun No. 1', 'aktif' => true, 'warna' => '#dc2626', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Puskesmas Pembantu', 'kategori' => 'fasilitas', 'lat' => -7.3560, 'lng' => 111.0102, 'deskripsi' => 'Pelayanan Kesehatan Dasar', 'alamat' => '', 'aktif' => true, 'warna' => '#16a34a', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'SDN 1 Karangtalun', 'kategori' => 'pendidikan', 'lat' => -7.3590, 'lng' => 111.0075, 'deskripsi' => 'Sekolah Dasar Negeri', 'alamat' => '', 'aktif' => true, 'warna' => '#2563eb', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Masjid Al-Ikhlas', 'kategori' => 'ibadah', 'lat' => -7.3568, 'lng' => 111.0093, 'deskripsi' => 'Masjid Desa', 'alamat' => '', 'aktif' => true, 'warna' => '#7c3aed', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pasar Desa', 'kategori' => 'ekonomi', 'lat' => -7.3580, 'lng' => 111.0065, 'deskripsi' => 'Pusat Perdagangan Lokal', 'alamat' => '', 'aktif' => true, 'warna' => '#d97706', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('cms_peta_desa')->insert($peta);

        // ═══ 19. CMS: INFOGRAFIS ════════════════════════════════════
        DB::table('cms_infografis')->insert([
            'apbdes_total' => 1480000000,
            'apbdes_realisasi' => 1130320000,
            'idm_skor' => 0.8152,
            'idm_status' => 'Maju',
            'stunting_total' => 312,
            'stunting_kasus' => 42,
            'data_bansos' => json_encode([
                ['program' => 'BLT Dana Desa', 'penerima' => 87, 'anggaran' => 'Rp 78,3 Jt'],
                ['program' => 'PKH', 'penerima' => 120, 'anggaran' => 'Rp 216,0 Jt'],
                ['program' => 'BPNT / Sembako', 'penerima' => 198, 'anggaran' => 'Rp 285,1 Jt'],
            ]),
            'sdgs_capaian' => json_encode([
                ['no' => 1, 'title' => 'Tanpa Kemiskinan', 'persen' => 68],
                ['no' => 2, 'title' => 'Tanpa Kelaparan', 'persen' => 82],
                ['no' => 3, 'title' => 'Kesehatan', 'persen' => 75],
                ['no' => 4, 'title' => 'Pendidikan', 'persen' => 79],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ═══ 20. CMS: HEADER & FOOTER ══════════════════════════════
        DB::table('cms_header_footer')->insert([
            'menu_navigasi' => json_encode([
                ['label' => 'Beranda', 'href' => '/', 'urutan' => 1],
                ['label' => 'Profil Desa', 'href' => '/profil', 'urutan' => 2],
                ['label' => 'Infografis', 'href' => '/infografis', 'urutan' => 3],
                ['label' => 'Peta Desa', 'href' => '/peta', 'urutan' => 4],
                ['label' => 'IDM', 'href' => '/idm', 'urutan' => 5],
                ['label' => 'Berita', 'href' => '/berita', 'urutan' => 6],
                ['label' => 'UMKM', 'href' => '/umkm', 'urutan' => 7],
                ['label' => 'Layanan', 'href' => '/layanan', 'urutan' => 8],
                ['label' => 'PPID', 'href' => '/ppid', 'urutan' => 9],
            ]),
            'teks_footer' => '© 2026 Pemerintah Desa Karangtalun. Hak Cipta Dilindungi.',
            'kontak_footer' => 'Jl. Raya Karangtalun No. 1, Kec. Tanon, Kab. Sragen, Jawa Tengah 57277',
            'jam_pelayanan' => 'Senin - Jumat: 08.00 - 15.00 WIB',
            'link_sosmed' => json_encode([
                ['platform' => 'Facebook', 'url' => 'https://facebook.com/desakarangtalun'],
                ['platform' => 'Instagram', 'url' => 'https://instagram.com/desakarangtalun'],
            ]),
            'tombol_wa' => '6281234567890',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
