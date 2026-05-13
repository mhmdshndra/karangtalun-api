<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warga\StoreSuratRequest;
use App\Http\Requests\Warga\StoreLaporanWargaRequest;
use App\Http\Requests\Warga\StorePermohonanWargaRequest;
use App\Http\Resources\PengajuanSuratResource;
use App\Http\Resources\LaporanAduanResource;
use App\Http\Resources\PermohonanInformasiResource;
use App\Models\AnggotaKK;
use App\Models\KartuKeluarga;
use App\Models\PengajuanSurat;
use App\Models\SuratLampiran;
use App\Models\LaporanAduan;
use App\Models\PermohonanInformasi;
use App\Services\NotificationService;
use App\Traits\ApiResponse;
use App\Traits\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WargaController extends Controller
{
    use ApiResponse, AuditLogger;

    // ── KK ─────────────────────────────────────────
    public function kk(Request $request): JsonResponse
    {
        $user = $request->user();
        $kk = KartuKeluarga::with('anggota')->where('no_kk', $user->no_kk)->first();
        if (!$kk) return $this->notFound('Data KK tidak ditemukan.');
        return $this->success($kk);
    }

    public function anggotaEligible(Request $request): JsonResponse
    {
        $user = $request->user();
        $kk = KartuKeluarga::with('anggota')->where('no_kk', $user->no_kk)->first();
        if (!$kk) return $this->notFound('Data KK tidak ditemukan.');

        $eligible = $kk->anggota->filter(fn($a) => $a->umur >= 17)->values();
        return $this->success($eligible);
    }

    // ── Surat ──────────────────────────────────────
    public function storeSurat(StoreSuratRequest $request): JsonResponse
    {
        $user = $request->user();

        // Lookup pemohon from anggota_kk — don't trust frontend data
        $pemohon = AnggotaKK::where('nik', $request->pemohon_nik)->first();
        if (!$pemohon) {
            return $this->error('NIK pemohon tidak ditemukan di database kependudukan.', 422, [
                'pemohon_nik' => ['NIK pemohon tidak valid.'],
            ]);
        }

        // Validate pemohon belongs to same KK as logged-in user
        if ($pemohon->no_kk !== $user->no_kk) {
            return $this->error('Pemohon bukan anggota Kartu Keluarga Anda.', 422, [
                'pemohon_nik' => ['Pemohon harus anggota KK yang sama dengan Anda.'],
            ]);
        }

        $kk = $pemohon->kartuKeluarga;
        $nomorTiket = self::generateUniqueTicket('TKT', 'pengajuan_surat', 'nomor_tiket');

        $jenisLabels = [
            'surat_keterangan_domisili' => 'Surat Keterangan Domisili',
            'surat_keterangan_tidak_mampu' => 'Surat Keterangan Tidak Mampu (SKTM)',
            'surat_pengantar_skck' => 'Surat Pengantar SKCK',
            'surat_keterangan_usaha' => 'Surat Keterangan Usaha',
        ];

        $alamatLengkap = implode(', ', array_filter([
            $kk?->alamat, $kk?->rt_rw,
            'Desa ' . ($kk?->kelurahan ?? 'Karangtalun'),
            'Kec. ' . ($kk?->kecamatan ?? ''),
            'Kab. ' . ($kk?->kabupaten ?? ''),
        ]));

        $surat = PengajuanSurat::create([
            'jenis_surat' => $request->jenis_surat,
            'nomor_tiket' => $nomorTiket,
            'status' => 'Menunggu',
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_diperbarui' => now()->toDateString(),
            'diajukan_oleh_user_id' => $user->id,
            'diajukan_oleh_nik' => $user->nik,
            'diajukan_oleh_nama' => $user->nama_lengkap,
            'pemohon_nik' => $pemohon->nik,
            'pemohon_nama_lengkap' => $pemohon->nama_lengkap,
            'pemohon_tempat_lahir' => $pemohon->tempat_lahir,
            'pemohon_tanggal_lahir' => $pemohon->tanggal_lahir->toDateString(),
            'pemohon_jenis_kelamin' => $pemohon->jenis_kelamin,
            'pemohon_pekerjaan' => $pemohon->pekerjaan,
            'pemohon_alamat' => $alamatLengkap,
            'pemohon_status_hubungan' => $pemohon->status_hubungan,
            'keperluan' => $request->keperluan,
        ]);

        if ($request->hasFile('berkas')) {
            foreach ($request->file('berkas') as $file) {
                $path = $file->store('surat-lampiran', 'public');
                SuratLampiran::create([
                    'pengajuan_surat_id' => $surat->id,
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        NotificationService::suratMasuk(
            $surat->nomor_tiket,
            $jenisLabels[$surat->jenis_surat] ?? $surat->jenis_surat,
            $surat->pemohon_nama_lengkap
        );
        $this->audit('create_surat', 'PengajuanSurat', (string) $surat->id);

        return $this->created(new PengajuanSuratResource($surat->load('lampiran')), 'Pengajuan surat berhasil dikirim.');
    }

    public function indexSurat(Request $request): JsonResponse
    {
        $surat = PengajuanSurat::with('lampiran')
            ->where('diajukan_oleh_user_id', $request->user()->id)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->jenis_surat, fn($q, $j) => $q->where('jenis_surat', $j))
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return $this->paginated($surat, 'Berhasil.', PengajuanSuratResource::class);
    }

    public function showSurat(Request $request, $id): JsonResponse
    {
        $surat = PengajuanSurat::with('lampiran')
            ->where('diajukan_oleh_user_id', $request->user()->id)
            ->find($id);

        if (!$surat) return $this->notFound('Surat tidak ditemukan.');
        return $this->success(new PengajuanSuratResource($surat));
    }

    public function uploadBerkas(Request $request, $id): JsonResponse
    {
        $request->validate(['berkas' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120']);

        $surat = PengajuanSurat::where('diajukan_oleh_user_id', $request->user()->id)->find($id);
        if (!$surat) return $this->notFound('Surat tidak ditemukan.');

        $file = $request->file('berkas');
        $path = $file->store('surat-lampiran', 'public');

        $lampiran = SuratLampiran::create([
            'pengajuan_surat_id' => $surat->id,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return $this->created($lampiran, 'Berkas berhasil diupload.');
    }

    // ── Laporan ────────────────────────────────────
    public function storeLaporan(StoreLaporanWargaRequest $request): JsonResponse
    {
        $user = $request->user();
        $nomorTiket = self::generateUniqueTicket('LAP', 'laporan_aduan', 'nomor_tiket');

        $laporan = LaporanAduan::create([
            'nomor_tiket' => $nomorTiket,
            'kategori' => $request->kategori,
            'nama_pelapor' => $user->nama_lengkap,
            'alamat_pelapor' => $user->alamat ?? '-',
            'kontak_pelapor' => $user->telepon ?? '-',
            'deskripsi' => $request->deskripsi,
            'lokasi_kejadian' => $request->lokasi_kejadian,
            'lokasi_gps' => $request->lokasi_gps,
            'status' => 'Dikirim',
            'pelapor_user_id' => $user->id,
            'pelapor_nik' => $user->nik,
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
        $this->audit('create_laporan', 'LaporanAduan', (string) $laporan->id);

        return $this->created(new LaporanAduanResource($laporan->load('lampiran')), 'Laporan berhasil dikirim.');
    }

    public function indexLaporan(Request $request): JsonResponse
    {
        $laporan = LaporanAduan::with('lampiran')
            ->where('pelapor_user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return $this->paginated($laporan, 'Berhasil.', LaporanAduanResource::class);
    }

    // ── Permohonan ─────────────────────────────────
    public function storePermohonan(StorePermohonanWargaRequest $request): JsonResponse
    {
        $user = $request->user();
        $nomor = self::generateUniqueTicket('PRM', 'permohonan_informasi', 'nomor_permohonan');

        $permohonan = PermohonanInformasi::create([
            'nomor_permohonan' => $nomor,
            'nama_pemohon' => $user->nama_lengkap,
            'alamat_pemohon' => $user->alamat ?? '-',
            'kontak_pemohon' => $user->telepon ?? '-',
            'tujuan_permohonan' => $request->tujuan_permohonan,
            'informasi_diminta' => $request->informasi_diminta,
            'status' => 'Dikirim',
            'pemohon_user_id' => $user->id,
            'pemohon_nik' => $user->nik,
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
        $this->audit('create_permohonan', 'PermohonanInformasi', (string) $permohonan->id);

        return $this->created(new PermohonanInformasiResource($permohonan->load('lampiran')), 'Permohonan berhasil dikirim.');
    }

    public function indexPermohonan(Request $request): JsonResponse
    {
        $permohonan = PermohonanInformasi::with('lampiran')
            ->where('pemohon_user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return $this->paginated($permohonan, 'Berhasil.', PermohonanInformasiResource::class);
    }

    // ── Unique ticket generator ────────────────────
    public static function generateUniqueTicket(string $prefix, string $table, string $column): string
    {
        for ($i = 0; $i < 10; $i++) {
            $ticket = $prefix . '-' . date('Y') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            if (!\DB::table($table)->where($column, $ticket)->exists()) {
                return $ticket;
            }
        }
        return $prefix . '-' . date('Ymd') . '-' . Str::random(6);
    }
}
