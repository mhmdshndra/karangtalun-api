<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staf\UpdateStatusSuratRequest;
use App\Http\Requests\Staf\UpdateStatusLaporanRequest;
use App\Http\Requests\Staf\UpdateStatusPermohonanRequest;
use App\Http\Resources\PengajuanSuratResource;
use App\Http\Resources\LaporanAduanResource;
use App\Http\Resources\PermohonanInformasiResource;
use App\Models\PengajuanSurat;
use App\Models\LaporanAduan;
use App\Models\PermohonanInformasi;
use App\Services\NotificationService;
use App\Traits\ApiResponse;
use App\Traits\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StafController extends Controller
{
    use ApiResponse, AuditLogger;

    // ── Surat ──────────────────────────────────────
    public function indexSurat(Request $request): JsonResponse
    {
        $query = PengajuanSurat::with('lampiran')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->jenis_surat, fn($q, $j) => $q->where('jenis_surat', $j))
            ->when($request->search, fn($q, $s) => $q->where(function ($q2) use ($s) {
                $q2->where('nomor_tiket', 'like', "%{$s}%")
                   ->orWhere('pemohon_nama_lengkap', 'like', "%{$s}%")
                   ->orWhere('diajukan_oleh_nama', 'like', "%{$s}%");
            }))
            ->orderByDesc('created_at');

        return $this->paginated($query->paginate($request->per_page ?? 15), 'Berhasil.', PengajuanSuratResource::class);
    }

    public function updateStatusSurat(UpdateStatusSuratRequest $request, $id): JsonResponse
    {
        $surat = PengajuanSurat::find($id);
        if (!$surat) return $this->notFound('Surat tidak ditemukan.');

        $oldStatus = $surat->status;
        $surat->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin ?? $surat->catatan_admin,
            'nomor_surat' => $request->nomor_surat ?? $surat->nomor_surat,
            'tanggal_diperbarui' => now()->toDateString(),
        ]);

        NotificationService::suratStatusChanged(
            $surat->nomor_tiket,
            $request->status,
            $surat->diajukan_oleh_user_id
        );
        $this->audit('update_status_surat', 'PengajuanSurat', (string) $surat->id,
            ['status' => $oldStatus], ['status' => $request->status]);

        return $this->success(new PengajuanSuratResource($surat->fresh()->load('lampiran')), 'Status surat berhasil diperbarui.');
    }

    // ── Laporan ────────────────────────────────────
    public function indexLaporan(Request $request): JsonResponse
    {
        $query = LaporanAduan::with('lampiran')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->kategori, fn($q, $k) => $q->where('kategori', $k))
            ->when($request->search, fn($q, $s) => $q->where(function ($q2) use ($s) {
                $q2->where('nomor_tiket', 'like', "%{$s}%")
                   ->orWhere('nama_pelapor', 'like', "%{$s}%")
                   ->orWhere('deskripsi', 'like', "%{$s}%");
            }))
            ->orderByDesc('created_at');

        return $this->paginated($query->paginate($request->per_page ?? 15), 'Berhasil.', LaporanAduanResource::class);
    }

    public function updateStatusLaporan(UpdateStatusLaporanRequest $request, $id): JsonResponse
    {
        $laporan = LaporanAduan::find($id);
        if (!$laporan) return $this->notFound('Laporan tidak ditemukan.');

        $oldStatus = $laporan->status;
        $laporan->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin ?? $laporan->catatan_admin,
        ]);

        NotificationService::laporanStatusChanged(
            $laporan->nomor_tiket,
            $request->status,
            $laporan->pelapor_user_id
        );
        $this->audit('update_status_laporan', 'LaporanAduan', (string) $laporan->id,
            ['status' => $oldStatus], ['status' => $request->status]);

        return $this->success(new LaporanAduanResource($laporan->fresh()->load('lampiran')), 'Status laporan berhasil diperbarui.');
    }

    // ── Permohonan ─────────────────────────────────
    public function indexPermohonan(Request $request): JsonResponse
    {
        $query = PermohonanInformasi::with('lampiran')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->where(function ($q2) use ($s) {
                $q2->where('nomor_permohonan', 'like', "%{$s}%")
                   ->orWhere('nama_pemohon', 'like', "%{$s}%")
                   ->orWhere('informasi_diminta', 'like', "%{$s}%");
            }))
            ->orderByDesc('created_at');

        return $this->paginated($query->paginate($request->per_page ?? 15), 'Berhasil.', PermohonanInformasiResource::class);
    }

    public function updateStatusPermohonan(UpdateStatusPermohonanRequest $request, $id): JsonResponse
    {
        $permohonan = PermohonanInformasi::find($id);
        if (!$permohonan) return $this->notFound('Permohonan tidak ditemukan.');

        $oldStatus = $permohonan->status;
        $permohonan->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin ?? $permohonan->catatan_admin,
        ]);

        if ($request->hasFile('file_balasan')) {
            $file = $request->file('file_balasan');
            $path = $file->store('permohonan-balasan', 'public');
            $permohonan->lampiran()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'tipe' => 'balasan_admin',
            ]);
        }

        NotificationService::permohonanStatusChanged(
            $permohonan->nomor_permohonan,
            $request->status,
            $permohonan->pemohon_user_id
        );
        $this->audit('update_status_permohonan', 'PermohonanInformasi', (string) $permohonan->id,
            ['status' => $oldStatus], ['status' => $request->status]);

        return $this->success(new PermohonanInformasiResource($permohonan->fresh()->load('lampiran')), 'Status permohonan berhasil diperbarui.');
    }
}
