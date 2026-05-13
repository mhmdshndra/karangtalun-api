<?php

namespace App\Services;

use App\Models\Notifikasi;

class NotificationService
{
    public static function create(array $data): Notifikasi
    {
        return Notifikasi::create(array_merge([
            'tanggal' => now(),
            'dibaca'  => false,
        ], $data));
    }

    // ── Surat Notifications ──

    public static function suratMasuk(string $nomorTiket, string $jenisSuratLabel, string $pemohonNama): void
    {
        self::create([
            'tipe'        => 'surat_masuk',
            'judul'       => 'Pengajuan Surat Baru Masuk',
            'pesan'       => "Pengajuan {$jenisSuratLabel} ({$nomorTiket}) atas nama {$pemohonNama} memerlukan verifikasi.",
            'link'        => '/staf/pengajuan-surat',
            'target_role' => 'staf_layanan',
        ]);
        self::create([
            'tipe'        => 'surat_masuk',
            'judul'       => 'Pengajuan Surat Baru Masuk',
            'pesan'       => "Pengajuan {$jenisSuratLabel} ({$nomorTiket}) atas nama {$pemohonNama} memerlukan verifikasi.",
            'link'        => '/admin/pengajuan-surat',
            'target_role' => 'admin_desa',
        ]);
    }

    public static function suratStatusChanged(string $nomorTiket, string $status, int $userId): void
    {
        $tipe = match ($status) {
            'Diproses' => 'surat_diproses',
            'Selesai'  => 'surat_selesai',
            'Ditolak'  => 'surat_ditolak',
            default    => 'surat_diproses',
        };

        $pesan = match ($status) {
            'Diproses' => "Pengajuan surat Anda ({$nomorTiket}) sedang diproses.",
            'Selesai'  => "Pengajuan surat Anda ({$nomorTiket}) telah selesai. Silakan ambil di kantor desa.",
            'Ditolak'  => "Pengajuan surat Anda ({$nomorTiket}) ditolak. Silakan cek catatan admin.",
            default    => "Status pengajuan surat Anda ({$nomorTiket}) telah diperbarui.",
        };

        self::create([
            'tipe'           => $tipe,
            'judul'          => "Surat {$status}",
            'pesan'          => $pesan,
            'link'           => '/warga/riwayat',
            'target_user_id' => $userId,
        ]);
    }

    // ── Laporan Notifications ──

    public static function laporanMasuk(string $nomorTiket, string $kategori): void
    {
        self::create([
            'tipe'        => 'laporan_masuk',
            'judul'       => "Laporan Baru: " . ucfirst($kategori),
            'pesan'       => "Laporan {$kategori} ({$nomorTiket}) baru masuk dan memerlukan tindak lanjut.",
            'link'        => '/admin/laporan-aduan',
            'target_role' => 'admin_desa',
        ]);
        self::create([
            'tipe'        => 'laporan_masuk',
            'judul'       => "Laporan Baru: " . ucfirst($kategori),
            'pesan'       => "Laporan {$kategori} ({$nomorTiket}) baru masuk dan memerlukan tindak lanjut.",
            'link'        => '/staf/laporan-aduan',
            'target_role' => 'staf_layanan',
        ]);
    }

    public static function laporanStatusChanged(string $nomorTiket, string $status, ?int $userId): void
    {
        if (!$userId) return;

        $tipe = match ($status) {
            'Ditindaklanjuti' => 'laporan_diproses',
            'Selesai'         => 'laporan_selesai',
            default           => 'laporan_diproses',
        };

        self::create([
            'tipe'           => $tipe,
            'judul'          => "Laporan {$status}",
            'pesan'          => "Laporan Anda ({$nomorTiket}) telah {$status}.",
            'link'           => '/warga/layanan/laporan-aduan',
            'target_user_id' => $userId,
        ]);
    }

    // ── Permohonan Notifications ──

    public static function permohonanMasuk(string $nomorPermohonan): void
    {
        self::create([
            'tipe'        => 'permohonan_masuk',
            'judul'       => 'Permohonan Informasi Baru',
            'pesan'       => "Permohonan informasi ({$nomorPermohonan}) baru masuk.",
            'link'        => '/admin/permohonan-ppid',
            'target_role' => 'admin_desa',
        ]);
        self::create([
            'tipe'        => 'permohonan_masuk',
            'judul'       => 'Permohonan Informasi Baru',
            'pesan'       => "Permohonan informasi ({$nomorPermohonan}) baru masuk.",
            'link'        => '/staf/permohonan-ppid',
            'target_role' => 'staf_layanan',
        ]);
    }

    public static function permohonanStatusChanged(string $nomorPermohonan, string $status, ?int $userId): void
    {
        if (!$userId) return;

        $tipe = match ($status) {
            'Diproses' => 'permohonan_diproses',
            'Dijawab'  => 'permohonan_selesai',
            'Ditolak'  => 'permohonan_ditolak',
            default    => 'permohonan_diproses',
        };

        self::create([
            'tipe'           => $tipe,
            'judul'          => "Permohonan {$status}",
            'pesan'          => "Permohonan informasi Anda ({$nomorPermohonan}) telah {$status}.",
            'link'           => '/warga/layanan/permohonan-informasi',
            'target_user_id' => $userId,
        ]);
    }
}
