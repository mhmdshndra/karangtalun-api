<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLaporanPublikRequest;
use App\Http\Requests\StorePermohonanPublikRequest;
use App\Models\LaporanAduan;
use App\Models\PermohonanInformasi;
use App\Models\Cms\CmsUmkm;
use App\Models\Cms\CmsBerita;
use App\Services\NotificationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PublicController extends Controller
{
    use ApiResponse;

    public function storeLaporan(StoreLaporanPublikRequest $request): JsonResponse
    {
        $nomorTiket = WargaController::generateUniqueTicket('LAP', 'laporan_aduan', 'nomor_tiket');

        $laporan = LaporanAduan::create([
            'nomor_tiket' => $nomorTiket,
            'kategori' => $request->kategori,
            'nama_pelapor' => $request->nama_pelapor,
            'alamat_pelapor' => $request->alamat_pelapor,
            'kontak_pelapor' => $request->kontak_pelapor,
            'deskripsi' => $request->deskripsi,
            'lokasi_kejadian' => $request->lokasi_kejadian,
            'lokasi_gps' => $request->lokasi_gps,
            'status' => 'Dikirim',
        ]);

        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $path = $file->store('laporan-lampiran', 'public');
                $laporan->lampiran()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        NotificationService::laporanMasuk($laporan->nomor_tiket, $laporan->kategori);

        return $this->created([
            'nomor_tiket' => $nomorTiket,
            'message' => 'Laporan berhasil dikirim.',
        ]);
    }

    public function storePermohonan(StorePermohonanPublikRequest $request): JsonResponse
    {
        $nomor = WargaController::generateUniqueTicket('PRM', 'permohonan_informasi', 'nomor_permohonan');

        $permohonan = PermohonanInformasi::create([
            'nomor_permohonan' => $nomor,
            'nama_pemohon' => $request->nama_pemohon,
            'alamat_pemohon' => $request->alamat_pemohon,
            'kontak_pemohon' => $request->kontak_pemohon,
            'tujuan_permohonan' => $request->tujuan_permohonan,
            'informasi_diminta' => $request->informasi_diminta,
            'status' => 'Dikirim',
        ]);

        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $path = $file->store('permohonan-lampiran', 'public');
                $permohonan->lampiran()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'tipe' => 'lampiran_pemohon',
                ]);
            }
        }

        NotificationService::permohonanMasuk($permohonan->nomor_permohonan);

        return $this->created([
            'nomor_permohonan' => $nomor,
            'message' => 'Permohonan berhasil dikirim.',
        ]);
    }

    public function likeUmkm($id): JsonResponse
    {
        $umkm = CmsUmkm::find($id);
        if (!$umkm) return $this->notFound('Produk UMKM tidak ditemukan.');

        $umkm->increment('likes');
        return $this->success(['likes' => $umkm->fresh()->likes], 'Like berhasil.');
    }

    public function viewBerita($slug): JsonResponse
    {
        $berita = CmsBerita::where('slug', $slug)->first();
        if (!$berita) return $this->notFound('Berita tidak ditemukan.');

        $berita->increment('views');
        return $this->success(['views' => $berita->fresh()->views]);
    }
}
