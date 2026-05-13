<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cms\CmsIdentitasDesaResource;
use App\Http\Resources\Cms\CmsProfilDesaResource;
use App\Http\Resources\Cms\CmsBeritaResource;
use App\Http\Resources\Cms\CmsGaleriResource;
use App\Http\Resources\Cms\CmsUmkmResource;
use App\Http\Resources\Cms\CmsAparaturResource;
use App\Http\Resources\Cms\CmsPotensiDesaResource;
use App\Http\Resources\Cms\CmsFasilitasResource;
use App\Http\Resources\Cms\CmsLayananPublikResource;
use App\Http\Resources\Cms\CmsPpidDokumenResource;
use App\Http\Resources\Cms\CmsPetaDesaResource;
use App\Http\Resources\Cms\CmsInfografisResource;
use App\Http\Resources\Cms\CmsHeaderFooterResource;
use App\Models\AnggotaKK;
use App\Models\KartuKeluarga;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CmsPublicController extends Controller
{
    use ApiResponse;

    public function identitasDesa(): JsonResponse
    {
        $model = \App\Models\Cms\CmsIdentitasDesa::first();
        if (!$model) return $this->success(null);
        return $this->success(new CmsIdentitasDesaResource($model));
    }

    public function profilDesa(): JsonResponse
    {
        $model = \App\Models\Cms\CmsProfilDesa::first();
        if (!$model) return $this->success(null);
        return $this->success(new CmsProfilDesaResource($model));
    }

    public function beritaIndex(Request $request): JsonResponse
    {
        $query = \App\Models\Cms\CmsBerita::where('status', 'Terbit')
            ->when($request->kategori, fn($q, $k) => $q->where('kategori', $k))
            ->when($request->search, fn($q, $s) => $q->where('judul', 'like', "%{$s}%"))
            ->when($request->featured, fn($q) => $q->where('is_featured', true))
            ->orderByDesc('created_at');

        return $this->paginated($query->paginate($request->per_page ?? 12), 'Berhasil.', CmsBeritaResource::class);
    }

    public function beritaShow($slug): JsonResponse
    {
        $berita = \App\Models\Cms\CmsBerita::where('slug', $slug)->where('status', 'Terbit')->first();
        if (!$berita) return $this->notFound('Berita tidak ditemukan.');
        return $this->success(new CmsBeritaResource($berita));
    }

    public function galeri(): JsonResponse
    {
        $items = \App\Models\Cms\CmsGaleri::where('aktif', true)->orderBy('urutan')->get();
        return $this->success(CmsGaleriResource::collection($items));
    }

    public function umkmIndex(Request $request): JsonResponse
    {
        $query = \App\Models\Cms\CmsUmkm::where('aktif', true)
            ->when($request->kategori, fn($q, $k) => $q->where('kategori', $k))
            ->when($request->search, fn($q, $s) => $q->where('nama', 'like', "%{$s}%"))
            ->when($request->unggulan, fn($q) => $q->where('unggulan', true))
            ->when($request->sort === 'terlaris', fn($q) => $q->orderByDesc('likes'))
            ->when($request->sort === 'terbaru', fn($q) => $q->orderByDesc('created_at'))
            ->when(!$request->sort, fn($q) => $q->orderByDesc('created_at'));

        return $this->paginated($query->paginate($request->per_page ?? 12), 'Berhasil.', CmsUmkmResource::class);
    }

    public function umkmShow($slug): JsonResponse
    {
        $umkm = \App\Models\Cms\CmsUmkm::where('slug', $slug)->where('aktif', true)->first();
        if (!$umkm) return $this->notFound('Produk UMKM tidak ditemukan.');
        return $this->success(new CmsUmkmResource($umkm));
    }

    public function aparatur(): JsonResponse
    {
        $items = \App\Models\Cms\CmsAparatur::where('aktif', true)->orderBy('urutan')->get();
        return $this->success(CmsAparaturResource::collection($items));
    }

    public function potensiDesa(): JsonResponse
    {
        $items = \App\Models\Cms\CmsPotensiDesa::where('aktif', true)->orderBy('urutan')->get();
        return $this->success(CmsPotensiDesaResource::collection($items));
    }

    public function fasilitas(): JsonResponse
    {
        $items = \App\Models\Cms\CmsFasilitas::where('aktif', true)
            ->with('titikLokasi')
            ->orderBy('urutan')
            ->get();
        return $this->success(CmsFasilitasResource::collection($items));
    }

    public function layananPublik(): JsonResponse
    {
        $items = \App\Models\Cms\CmsLayananPublik::where('aktif', true)->get();
        return $this->success(CmsLayananPublikResource::collection($items));
    }

    public function ppidDokumen(Request $request): JsonResponse
    {
        $query = \App\Models\Cms\CmsPpidDokumen::where('aktif', true)
            ->when($request->kategori, fn($q, $k) => $q->where('kategori', $k))
            ->orderBy('urutan');
        return $this->success(CmsPpidDokumenResource::collection($query->get()));
    }

    public function petaDesa(): JsonResponse
    {
        $items = \App\Models\Cms\CmsPetaDesa::where('aktif', true)->get();
        return $this->success(CmsPetaDesaResource::collection($items));
    }

    public function infografis(): JsonResponse
    {
        $infografis = \App\Models\Cms\CmsInfografis::first();
        $computed = $this->computeKependudukan();

        if (!$infografis) {
            return $this->success([
                'jumlahPenduduk' => $computed['total_penduduk'],
                'jumlahKK' => $computed['total_kk'],
                'apbdesTotal' => 0,
                'apbdesRealisasi' => 0,
                'idmSkor' => 0,
                'idmStatus' => null,
                'stuntingTotal' => 0,
                'stuntingKasus' => 0,
                'dataBansos' => [],
                'sdgsCapaian' => [],
            ]);
        }

        $resource = new CmsInfografisResource($infografis);
        $resource->additional([
            'jumlah_penduduk' => $computed['total_penduduk'],
            'jumlah_kk' => $computed['total_kk'],
        ]);
        return $this->success($resource);
    }

    public function headerFooter(): JsonResponse
    {
        $model = \App\Models\Cms\CmsHeaderFooter::first();
        if (!$model) return $this->success(null);
        return $this->success(new CmsHeaderFooterResource($model));
    }

    public function statistikKependudukan(): JsonResponse
    {
        return $this->success($this->computeKependudukan());
    }

    private function computeKependudukan(): array
    {
        $totalPenduduk = AnggotaKK::count();
        $totalKK = KartuKeluarga::count();

        $gender = AnggotaKK::select('jenis_kelamin', DB::raw('count(*) as jumlah'))
            ->groupBy('jenis_kelamin')->pluck('jumlah', 'jenis_kelamin')->toArray();

        $pendidikan = AnggotaKK::select('pendidikan', DB::raw('count(*) as jumlah'))
            ->groupBy('pendidikan')->pluck('jumlah', 'pendidikan')->toArray();

        $pekerjaan = AnggotaKK::select('pekerjaan', DB::raw('count(*) as jumlah'))
            ->groupBy('pekerjaan')->pluck('jumlah', 'pekerjaan')->toArray();

        $agama = AnggotaKK::select('agama', DB::raw('count(*) as jumlah'))
            ->groupBy('agama')->pluck('jumlah', 'agama')->toArray();

        $kelompokUsia = [
            '0-5' => AnggotaKK::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 5')->count(),
            '6-12' => AnggotaKK::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 6 AND 12')->count(),
            '13-17' => AnggotaKK::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 13 AND 17')->count(),
            '18-25' => AnggotaKK::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 25')->count(),
            '26-35' => AnggotaKK::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 26 AND 35')->count(),
            '36-45' => AnggotaKK::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 36 AND 45')->count(),
            '46-55' => AnggotaKK::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 46 AND 55')->count(),
            '56-65' => AnggotaKK::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 56 AND 65')->count(),
            '65+' => AnggotaKK::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) > 65')->count(),
        ];

        return [
            'total_penduduk' => $totalPenduduk,
            'total_kk' => $totalKK,
            'gender' => $gender,
            'pendidikan' => $pendidikan,
            'pekerjaan' => $pekerjaan,
            'agama' => $agama,
            'kelompok_usia' => $kelompokUsia,
        ];
    }
}
