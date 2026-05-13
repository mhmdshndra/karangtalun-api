<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cms\CmsAparaturResource;
use App\Http\Resources\Cms\CmsBeritaResource;
use App\Http\Resources\Cms\CmsFasilitasResource;
use App\Http\Resources\Cms\CmsGaleriResource;
use App\Http\Resources\Cms\CmsHeaderFooterResource;
use App\Http\Resources\Cms\CmsIdentitasDesaResource;
use App\Http\Resources\Cms\CmsInfografisResource;
use App\Http\Resources\Cms\CmsLayananPublikResource;
use App\Http\Resources\Cms\CmsPetaDesaResource;
use App\Http\Resources\Cms\CmsPotensiDesaResource;
use App\Http\Resources\Cms\CmsPpidDokumenResource;
use App\Http\Resources\Cms\CmsProfilDesaResource;
use App\Http\Resources\Cms\CmsUmkmResource;
use App\Traits\ApiResponse;
use App\Traits\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsAdminController extends Controller
{
    use ApiResponse, AuditLogger;

    // ── Helper: map camelCase frontend keys to snake_case DB columns ──
    private function mapIdentitasDesa(Request $request): array
    {
        $map = [
            'namaDesa' => 'nama_desa', 'kodeDesa' => 'kode_desa',
            'kodePos' => 'kode_pos', 'mapsUrl' => 'maps_url',
            'namaKades' => 'nama_kades', 'jabatanKades' => 'jabatan_kades',
            'tahunAnggaran' => 'tahun_anggaran',
        ];
        $data = [];
        foreach ($map as $camel => $snake) {
            if ($request->has($camel)) $data[$snake] = $request->input($camel);
            if ($request->has($snake)) $data[$snake] = $request->input($snake);
        }
        if ($request->has('koordinat.lat')) $data['koordinat_lat'] = $request->input('koordinat.lat');
        if ($request->has('koordinat.lng')) $data['koordinat_lng'] = $request->input('koordinat.lng');
        if ($request->has('koordinat_lat')) $data['koordinat_lat'] = $request->input('koordinat_lat');
        if ($request->has('koordinat_lng')) $data['koordinat_lng'] = $request->input('koordinat_lng');
        $sosmedMap = ['facebook' => 'sosmed_facebook', 'instagram' => 'sosmed_instagram', 'twitter' => 'sosmed_twitter', 'youtube' => 'sosmed_youtube', 'tiktok' => 'sosmed_tiktok'];
        foreach ($sosmedMap as $key => $col) {
            if ($request->has("sosialMedia.{$key}")) $data[$col] = $request->input("sosialMedia.{$key}");
            if ($request->has($col)) $data[$col] = $request->input($col);
        }
        foreach (['nama_desa','kode_desa','kecamatan','kabupaten','provinsi','kode_pos','alamat','email','telepon','maps_url','koordinat_lat','koordinat_lng','nama_kades','jabatan_kades','tahun_anggaran'] as $f) {
            if ($request->has($f) && !isset($data[$f])) $data[$f] = $request->input($f);
        }
        return $data;
    }

    private function deleteOldFile(?string $path, string $disk = 'public'): void
    {
        if ($path && !str_starts_with($path, '/assets') && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    // ══════════════════════════════════════════════════════════════
    // Singletons (PUT only)
    // ══════════════════════════════════════════════════════════════

    public function updateIdentitasDesa(Request $request): JsonResponse
    {
        $model = \App\Models\Cms\CmsIdentitasDesa::first();
        if (!$model) return $this->error('Data identitas desa belum diinisialisasi.', 500);
        $data = $this->mapIdentitasDesa($request);
        $old = $model->toArray();
        $model->update($data);
        $this->audit('update_cms', 'CmsIdentitasDesa', (string) $model->id, $old, $data);
        return $this->success(new CmsIdentitasDesaResource($model->fresh()), 'Identitas desa berhasil diperbarui.');
    }

    public function updateProfilDesa(Request $request): JsonResponse
    {
        $request->validate([
            'foto_kades' => 'sometimes|nullable|image|max:5120',
            'sambutan' => 'sometimes|nullable|string', 'misi' => 'sometimes|nullable|array', 'misi.*' => 'string',
            'struktur_pemerintahan' => 'sometimes|nullable|string', 'fasilitas_teks' => 'sometimes|nullable|string',
            'sejarah' => 'sometimes|nullable|string', 'visi' => 'sometimes|nullable|string', 'potensi' => 'sometimes|nullable|string',
        ]);
        $model = \App\Models\Cms\CmsProfilDesa::first();
        if (!$model) return $this->error('Data profil desa belum diinisialisasi.', 500);

        $data = $request->only(['sejarah', 'visi', 'misi', 'potensi', 'sambutan', 'struktur_pemerintahan', 'fasilitas_teks']);
        if ($request->has('strukturPemerintahan')) $data['struktur_pemerintahan'] = $request->input('strukturPemerintahan');
        if ($request->has('fasilitas')) $data['fasilitas_teks'] = $request->input('fasilitas');

        if ($request->hasFile('foto_kades') || $request->hasFile('fotoKades')) {
            $file = $request->file('foto_kades') ?? $request->file('fotoKades');
            $this->deleteOldFile($model->foto_kades);
            $data['foto_kades'] = $file->store('cms/profil', 'public');
        }
        $model->update($data);
        $this->audit('update_cms', 'CmsProfilDesa', (string) $model->id);
        return $this->success(new CmsProfilDesaResource($model->fresh()), 'Profil desa berhasil diperbarui.');
    }

    public function updateInfografis(Request $request): JsonResponse
    {
        $model = \App\Models\Cms\CmsInfografis::first();
        if (!$model) return $this->error('Data infografis belum diinisialisasi.', 500);
        $camelMap = [
            'apbdesTotal' => 'apbdes_total', 'apbdesRealisasi' => 'apbdes_realisasi',
            'idmSkor' => 'idm_skor', 'idmStatus' => 'idm_status',
            'stuntingTotal' => 'stunting_total', 'stuntingKasus' => 'stunting_kasus',
            'dataBansos' => 'data_bansos', 'sdgsCapaian' => 'sdgs_capaian',
        ];
        $data = $request->only(['apbdes_total', 'apbdes_realisasi', 'idm_skor', 'idm_status', 'stunting_total', 'stunting_kasus', 'data_bansos', 'sdgs_capaian']);
        foreach ($camelMap as $camel => $snake) {
            if ($request->has($camel)) $data[$snake] = $request->input($camel);
        }
        $model->update($data);
        $this->audit('update_cms', 'CmsInfografis', (string) $model->id);
        return $this->success(new CmsInfografisResource($model->fresh()), 'Infografis berhasil diperbarui.');
    }

    public function updateHeaderFooter(Request $request): JsonResponse
    {
        $model = \App\Models\Cms\CmsHeaderFooter::first();
        if (!$model) return $this->error('Data header/footer belum diinisialisasi.', 500);
        $camelMap = [
            'menuNavigasi' => 'menu_navigasi', 'teksFooter' => 'teks_footer',
            'kontakFooter' => 'kontak_footer', 'jamPelayanan' => 'jam_pelayanan',
            'linkSosmed' => 'link_sosmed', 'tombolWa' => 'tombol_wa',
        ];
        $data = $request->only(['menu_navigasi', 'teks_footer', 'kontak_footer', 'jam_pelayanan', 'link_sosmed', 'tombol_wa']);
        foreach ($camelMap as $camel => $snake) {
            if ($request->has($camel)) $data[$snake] = $request->input($camel);
        }
        $model->update($data);
        $this->audit('update_cms', 'CmsHeaderFooter', (string) $model->id);
        return $this->success(new CmsHeaderFooterResource($model->fresh()), 'Header & footer berhasil diperbarui.');
    }

    // ══════════════════════════════════════════════════════════════
    // Berita CRUD
    // ══════════════════════════════════════════════════════════════

    public function storeBerita(Request $request): JsonResponse
    {
        $request->validate([
            'judul' => 'required|string|max:500', 'konten' => 'required|string',
            'kategori' => 'required|string|max:100', 'thumbnail' => 'nullable|image|max:5120',
            'status' => 'nullable|in:Terbit,Draft', 'tipe' => 'nullable|string|max:50', 'is_featured' => 'nullable|boolean',
        ]);
        $data = $request->only(['judul', 'konten', 'kategori', 'penulis', 'tanggal', 'waktu', 'status', 'tipe', 'link_video']);
        $data['slug'] = Str::slug($request->judul) . '-' . Str::random(5);
        $data['penulis'] = $data['penulis'] ?? $request->user()->nama_lengkap;
        $data['tanggal'] = $data['tanggal'] ?? now()->format('d M Y');
        $data['waktu'] = $data['waktu'] ?? now()->format('H:i') . ' WIB';
        $data['status'] = $data['status'] ?? 'Terbit';
        $data['tipe'] = $data['tipe'] ?? 'Artikel';
        $data['views'] = 0;
        $data['is_featured'] = $request->boolean('is_featured', false);
        if ($request->has('linkVideo')) $data['link_video'] = $request->input('linkVideo');
        if ($request->has('isFeatured')) $data['is_featured'] = $request->boolean('isFeatured', false);
        if ($request->hasFile('thumbnail')) $data['thumbnail'] = $request->file('thumbnail')->store('cms/berita', 'public');

        $berita = \App\Models\Cms\CmsBerita::create($data);
        $this->audit('create_cms', 'CmsBerita', (string) $berita->id);
        return $this->created(new CmsBeritaResource($berita));
    }

    public function updateBerita(Request $request, $id): JsonResponse
    {
        $berita = \App\Models\Cms\CmsBerita::find($id);
        if (!$berita) return $this->notFound('Berita tidak ditemukan.');
        $data = $request->only(['judul', 'konten', 'kategori', 'penulis', 'tanggal', 'waktu', 'status', 'tipe', 'link_video']);
        if ($request->has('linkVideo')) $data['link_video'] = $request->input('linkVideo');
        if ($request->has('isFeatured')) $data['is_featured'] = $request->boolean('isFeatured', false);
        if ($request->has('is_featured')) $data['is_featured'] = $request->boolean('is_featured', false);
        if ($request->hasFile('thumbnail')) {
            $this->deleteOldFile($berita->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('cms/berita', 'public');
        }
        if ($request->has('judul') && $request->judul !== $berita->judul) {
            $data['slug'] = Str::slug($request->judul) . '-' . Str::random(5);
        }
        $berita->update($data);
        $this->audit('update_cms', 'CmsBerita', (string) $berita->id);
        return $this->success(new CmsBeritaResource($berita->fresh()));
    }

    public function destroyBerita($id): JsonResponse
    {
        $berita = \App\Models\Cms\CmsBerita::find($id);
        if (!$berita) return $this->notFound('Berita tidak ditemukan.');
        $this->deleteOldFile($berita->thumbnail);
        $this->audit('delete_cms', 'CmsBerita', (string) $berita->id);
        $berita->delete();
        return $this->success(null, 'Berita berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════
    // Galeri
    // ══════════════════════════════════════════════════════════════

    public function storeGaleri(Request $request): JsonResponse
    {
        $request->validate(['judul' => 'required|string|max:255', 'deskripsi' => 'nullable|string|max:1000', 'kategori' => 'nullable|string|max:100', 'url' => 'nullable|string|max:500', 'foto' => 'nullable|image|max:5120']);
        $data = $request->only(['judul', 'deskripsi', 'kategori', 'url', 'urutan']);
        if ($request->hasFile('foto')) $data['url'] = $request->file('foto')->store('cms/galeri', 'public');
        $data['aktif'] = $request->boolean('aktif', true);
        $data['tanggal'] = $request->input('tanggal', now()->format('Y-m-d'));
        $data['kategori'] = $data['kategori'] ?? 'Umum';
        $item = \App\Models\Cms\CmsGaleri::create($data);
        $this->audit('create_cms', 'CmsGaleri', (string) $item->id);
        return $this->created(new CmsGaleriResource($item));
    }

    public function updateGaleri(Request $request, $id): JsonResponse
    {
        $galeri = \App\Models\Cms\CmsGaleri::find($id);
        if (!$galeri) return $this->notFound('Data tidak ditemukan.');
        $data = $request->only(['judul', 'deskripsi', 'kategori', 'tanggal', 'urutan', 'aktif']);
        if ($request->hasFile('foto')) { $this->deleteOldFile($galeri->url); $data['url'] = $request->file('foto')->store('cms/galeri', 'public'); }
        elseif ($request->has('url')) { $data['url'] = $request->input('url'); }
        $galeri->update($data);
        $this->audit('update_cms', 'CmsGaleri', (string) $galeri->id);
        return $this->success(new CmsGaleriResource($galeri->fresh()));
    }

    public function destroyGaleri($id): JsonResponse
    {
        $galeri = \App\Models\Cms\CmsGaleri::find($id);
        if (!$galeri) return $this->notFound('Data tidak ditemukan.');
        $this->deleteOldFile($galeri->url);
        $this->audit('delete_cms', 'CmsGaleri', (string) $galeri->id);
        $galeri->delete();
        return $this->success(null, 'Data berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════
    // UMKM
    // ══════════════════════════════════════════════════════════════

    public function storeUmkm(Request $request): JsonResponse
    {
        $request->validate(['nama' => 'required|string|max:255', 'deskripsi' => 'required|string', 'kategori' => 'required|string|max:100', 'foto' => 'nullable|image|max:5120']);
        $data = $request->only(['nama', 'kategori', 'deskripsi', 'whatsapp']);
        $data['slug'] = Str::slug($request->nama) . '-' . Str::random(5);
        $data['aktif'] = $request->boolean('aktif', true);
        $data['unggulan'] = $request->boolean('unggulan', false);
        $data['likes'] = 0;
        $data['nama_penjual'] = $request->input('namaPenjual', $request->input('nama_penjual', ''));
        $data['rt_rw'] = $request->input('rtRw', $request->input('rt_rw', ''));
        $data['whatsapp'] = $data['whatsapp'] ?? '';
        $data['harga'] = (int) ($request->input('harga', 0));
        if ($request->hasFile('foto')) $data['foto'] = $request->file('foto')->store('cms/umkm', 'public');
        $item = \App\Models\Cms\CmsUmkm::create($data);
        $this->audit('create_cms', 'CmsUmkm', (string) $item->id);
        return $this->created(new CmsUmkmResource($item));
    }

    public function updateUmkm(Request $request, $id): JsonResponse
    {
        $umkm = \App\Models\Cms\CmsUmkm::find($id);
        if (!$umkm) return $this->notFound('Data tidak ditemukan.');
        $data = $request->only(['nama', 'kategori', 'deskripsi', 'whatsapp', 'aktif', 'unggulan']);
        if ($request->has('namaPenjual')) $data['nama_penjual'] = $request->input('namaPenjual');
        if ($request->has('nama_penjual')) $data['nama_penjual'] = $request->input('nama_penjual');
        if ($request->has('rtRw')) $data['rt_rw'] = $request->input('rtRw');
        if ($request->has('rt_rw')) $data['rt_rw'] = $request->input('rt_rw');
        if ($request->has('harga')) $data['harga'] = (int) $request->input('harga');
        if ($request->hasFile('foto')) { $this->deleteOldFile($umkm->foto); $data['foto'] = $request->file('foto')->store('cms/umkm', 'public'); }
        if ($request->has('nama') && $request->nama !== $umkm->nama) $data['slug'] = Str::slug($request->nama) . '-' . Str::random(5);
        $umkm->update($data);
        $this->audit('update_cms', 'CmsUmkm', (string) $umkm->id);
        return $this->success(new CmsUmkmResource($umkm->fresh()));
    }

    public function destroyUmkm($id): JsonResponse
    {
        $umkm = \App\Models\Cms\CmsUmkm::find($id);
        if (!$umkm) return $this->notFound('Data tidak ditemukan.');
        $this->deleteOldFile($umkm->foto);
        $this->audit('delete_cms', 'CmsUmkm', (string) $umkm->id);
        $umkm->delete();
        return $this->success(null, 'Data berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════
    // Aparatur
    // ══════════════════════════════════════════════════════════════

    public function storeAparatur(Request $request): JsonResponse
    {
        $request->validate(['nama' => 'required|string|max:255', 'jabatan' => 'required|string|max:255', 'foto' => 'nullable|image|max:5120']);
        $data = $request->only(['nama', 'jabatan', 'urutan']);
        $data['aktif'] = $request->boolean('aktif', true);
        $data['kategori_jabatan'] = $request->input('kategoriJabatan', $request->input('kategori_jabatan', 'Lainnya'));
        if ($request->hasFile('foto')) $data['foto'] = $request->file('foto')->store('cms/aparatur', 'public');
        $item = \App\Models\Cms\CmsAparatur::create($data);
        $this->audit('create_cms', 'CmsAparatur', (string) $item->id);
        return $this->created(new CmsAparaturResource($item));
    }

    public function updateAparatur(Request $request, $id): JsonResponse
    {
        $item = \App\Models\Cms\CmsAparatur::find($id);
        if (!$item) return $this->notFound('Data tidak ditemukan.');
        $data = $request->only(['nama', 'jabatan', 'urutan', 'aktif']);
        if ($request->has('kategoriJabatan')) $data['kategori_jabatan'] = $request->input('kategoriJabatan');
        if ($request->has('kategori_jabatan')) $data['kategori_jabatan'] = $request->input('kategori_jabatan');
        if ($request->hasFile('foto')) { $this->deleteOldFile($item->foto); $data['foto'] = $request->file('foto')->store('cms/aparatur', 'public'); }
        $item->update($data);
        $this->audit('update_cms', 'CmsAparatur', (string) $item->id);
        return $this->success(new CmsAparaturResource($item->fresh()));
    }

    public function destroyAparatur($id): JsonResponse
    {
        $item = \App\Models\Cms\CmsAparatur::find($id);
        if (!$item) return $this->notFound('Data tidak ditemukan.');
        $this->deleteOldFile($item->foto);
        $this->audit('delete_cms', 'CmsAparatur', (string) $item->id);
        $item->delete();
        return $this->success(null, 'Data berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════
    // Potensi Desa
    // ══════════════════════════════════════════════════════════════

    public function storePotensiDesa(Request $request): JsonResponse
    {
        $request->validate(['judul' => 'required|string|max:255', 'deskripsi' => 'nullable|string', 'gambar' => 'nullable|image|max:5120']);
        $data = $request->only(['judul', 'deskripsi', 'urutan']);
        $data['deskripsi'] = $data['deskripsi'] ?? '';
        $data['aktif'] = $request->boolean('aktif', true);
        if ($request->hasFile('gambar')) $data['gambar'] = $request->file('gambar')->store('cms/potensi', 'public');
        $item = \App\Models\Cms\CmsPotensiDesa::create($data);
        $this->audit('create_cms', 'CmsPotensiDesa', (string) $item->id);
        return $this->created(new CmsPotensiDesaResource($item));
    }

    public function updatePotensiDesa(Request $request, $id): JsonResponse
    {
        $item = \App\Models\Cms\CmsPotensiDesa::find($id);
        if (!$item) return $this->notFound('Data tidak ditemukan.');
        $data = $request->only(['judul', 'deskripsi', 'urutan', 'aktif']);
        if ($request->hasFile('gambar')) { $this->deleteOldFile($item->gambar); $data['gambar'] = $request->file('gambar')->store('cms/potensi', 'public'); }
        $item->update($data);
        $this->audit('update_cms', 'CmsPotensiDesa', (string) $item->id);
        return $this->success(new CmsPotensiDesaResource($item->fresh()));
    }

    public function destroyPotensiDesa($id): JsonResponse
    {
        $item = \App\Models\Cms\CmsPotensiDesa::find($id);
        if (!$item) return $this->notFound('Data tidak ditemukan.');
        $this->deleteOldFile($item->gambar);
        $this->audit('delete_cms', 'CmsPotensiDesa', (string) $item->id);
        $item->delete();
        return $this->success(null, 'Data berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════
    // Fasilitas
    // ══════════════════════════════════════════════════════════════

    public function storeFasilitas(Request $request): JsonResponse
    {
        $request->validate(['nama' => 'required|string|max:255', 'gambar' => 'nullable|image|max:5120']);
        $data = $request->only(['nama', 'deskripsi', 'label', 'urutan']);
        if ($request->hasFile('gambar')) $data['gambar'] = $request->file('gambar')->store('cms/fasilitas', 'public');
        $data['aktif'] = $request->boolean('aktif', true);
        $fasilitas = \App\Models\Cms\CmsFasilitas::create($data);

        $titikLokasi = $request->input('titik_lokasi', $request->input('titikLokasi', []));
        if (is_array($titikLokasi)) {
            foreach ($titikLokasi as $tl) {
                $fasilitas->titikLokasi()->create([
                    'nama' => $tl['nama'] ?? '', 'lat' => $tl['lat'] ?? $tl['latitude'] ?? 0,
                    'lng' => $tl['lng'] ?? $tl['longitude'] ?? 0, 'label' => $tl['label'] ?? null,
                    'route_link' => $tl['routeLink'] ?? $tl['route_link'] ?? null,
                ]);
            }
        }
        $this->audit('create_cms', 'CmsFasilitas', (string) $fasilitas->id);
        return $this->created(new CmsFasilitasResource($fasilitas->load('titikLokasi')));
    }

    public function updateFasilitas(Request $request, $id): JsonResponse
    {
        $fasilitas = \App\Models\Cms\CmsFasilitas::find($id);
        if (!$fasilitas) return $this->notFound('Fasilitas tidak ditemukan.');
        $data = $request->only(['nama', 'deskripsi', 'label', 'urutan', 'aktif']);
        if ($request->hasFile('gambar')) { $this->deleteOldFile($fasilitas->gambar); $data['gambar'] = $request->file('gambar')->store('cms/fasilitas', 'public'); }
        $fasilitas->update($data);

        $titikLokasi = $request->input('titik_lokasi', $request->input('titikLokasi'));
        if (is_array($titikLokasi)) {
            $fasilitas->titikLokasi()->delete();
            foreach ($titikLokasi as $tl) {
                $fasilitas->titikLokasi()->create([
                    'nama' => $tl['nama'] ?? '', 'lat' => $tl['lat'] ?? $tl['latitude'] ?? 0,
                    'lng' => $tl['lng'] ?? $tl['longitude'] ?? 0, 'label' => $tl['label'] ?? null,
                    'route_link' => $tl['routeLink'] ?? $tl['route_link'] ?? null,
                ]);
            }
        }
        $this->audit('update_cms', 'CmsFasilitas', (string) $fasilitas->id);
        return $this->success(new CmsFasilitasResource($fasilitas->fresh()->load('titikLokasi')));
    }

    public function destroyFasilitas($id): JsonResponse
    {
        $fasilitas = \App\Models\Cms\CmsFasilitas::find($id);
        if (!$fasilitas) return $this->notFound('Data tidak ditemukan.');
        $this->deleteOldFile($fasilitas->gambar);
        $fasilitas->titikLokasi()->delete();
        $this->audit('delete_cms', 'CmsFasilitas', (string) $fasilitas->id);
        $fasilitas->delete();
        return $this->success(null, 'Data berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════
    // Layanan Publik
    // ══════════════════════════════════════════════════════════════

    public function storeLayananPublik(Request $request): JsonResponse
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $data = $request->only(['nama', 'deskripsi', 'kategori', 'estimasi_waktu', 'biaya', 'persyaratan', 'prosedur', 'instruksi', 'route_slug', 'tipe_layanan']);
        if ($request->has('estimasiWaktu')) $data['estimasi_waktu'] = $request->input('estimasiWaktu');
        if ($request->has('butuhLogin')) $data['butuh_login'] = $request->boolean('butuhLogin');
        if ($request->has('routeSlug')) $data['route_slug'] = $request->input('routeSlug');
        if ($request->has('tipeLayanan')) $data['tipe_layanan'] = $request->input('tipeLayanan');
        $data['aktif'] = $request->boolean('aktif', true);
        // NOT NULL defaults
        $data['deskripsi'] = $data['deskripsi'] ?? '';
        $data['kategori'] = $data['kategori'] ?? 'Umum';
        $data['route_slug'] = $data['route_slug'] ?? \Illuminate\Support\Str::slug($request->input('nama'));
        $data['tipe_layanan'] = $data['tipe_layanan'] ?? 'surat';
        $item = \App\Models\Cms\CmsLayananPublik::create($data);
        $this->audit('create_cms', 'CmsLayananPublik', (string) $item->id);
        return $this->created(new CmsLayananPublikResource($item));
    }

    public function updateLayananPublik(Request $request, $id): JsonResponse
    {
        $item = \App\Models\Cms\CmsLayananPublik::find($id);
        if (!$item) return $this->notFound('Data tidak ditemukan.');
        $data = $request->only(['nama', 'deskripsi', 'kategori', 'estimasi_waktu', 'biaya', 'persyaratan', 'prosedur', 'aktif', 'instruksi', 'route_slug', 'tipe_layanan']);
        if ($request->has('estimasiWaktu')) $data['estimasi_waktu'] = $request->input('estimasiWaktu');
        if ($request->has('butuhLogin')) $data['butuh_login'] = $request->boolean('butuhLogin');
        if ($request->has('routeSlug')) $data['route_slug'] = $request->input('routeSlug');
        if ($request->has('tipeLayanan')) $data['tipe_layanan'] = $request->input('tipeLayanan');
        $item->update($data);
        $this->audit('update_cms', 'CmsLayananPublik', (string) $item->id);
        return $this->success(new CmsLayananPublikResource($item->fresh()));
    }

    public function destroyLayananPublik($id): JsonResponse
    {
        $item = \App\Models\Cms\CmsLayananPublik::find($id);
        if (!$item) return $this->notFound('Data tidak ditemukan.');
        $this->audit('delete_cms', 'CmsLayananPublik', (string) $item->id);
        $item->delete();
        return $this->success(null, 'Data berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════
    // PPID Dokumen
    // ══════════════════════════════════════════════════════════════

    public function storePpidDokumen(Request $request): JsonResponse
    {
        $request->validate(['judul' => 'required|string|max:255', 'kategori' => 'required|string|max:100']);
        $data = $request->only(['judul', 'kategori', 'tanggal', 'urutan']);
        $data['aktif'] = $request->boolean('aktif', true);
        $data['tanggal'] = $data['tanggal'] ?? now()->format('Y-m-d');
        if ($request->hasFile('file_url') || $request->hasFile('fileUrl')) {
            $file = $request->file('file_url') ?? $request->file('fileUrl');
            $data['file_url'] = $file->store('cms/ppid', 'public');
        } elseif ($request->has('fileUrl')) {
            $data['file_url'] = $request->input('fileUrl');
        } elseif ($request->has('file_url')) {
            $data['file_url'] = $request->input('file_url');
        } else {
            $data['file_url'] = '#';
        }
        $item = \App\Models\Cms\CmsPpidDokumen::create($data);
        $this->audit('create_cms', 'CmsPpidDokumen', (string) $item->id);
        return $this->created(new CmsPpidDokumenResource($item));
    }

    public function updatePpidDokumen(Request $request, $id): JsonResponse
    {
        $item = \App\Models\Cms\CmsPpidDokumen::find($id);
        if (!$item) return $this->notFound('Data tidak ditemukan.');
        $data = $request->only(['judul', 'kategori', 'tanggal', 'urutan', 'aktif']);
        if ($request->hasFile('file_url') || $request->hasFile('fileUrl')) {
            $file = $request->file('file_url') ?? $request->file('fileUrl');
            if ($item->file_url && $item->file_url !== '#') $this->deleteOldFile($item->file_url);
            $data['file_url'] = $file->store('cms/ppid', 'public');
        } elseif ($request->has('fileUrl')) {
            $data['file_url'] = $request->input('fileUrl');
        }
        $item->update($data);
        $this->audit('update_cms', 'CmsPpidDokumen', (string) $item->id);
        return $this->success(new CmsPpidDokumenResource($item->fresh()));
    }

    public function destroyPpidDokumen($id): JsonResponse
    {
        $item = \App\Models\Cms\CmsPpidDokumen::find($id);
        if (!$item) return $this->notFound('Data tidak ditemukan.');
        if ($item->file_url && $item->file_url !== '#') $this->deleteOldFile($item->file_url);
        $this->audit('delete_cms', 'CmsPpidDokumen', (string) $item->id);
        $item->delete();
        return $this->success(null, 'Data berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════
    // Peta Desa
    // ══════════════════════════════════════════════════════════════

    public function storePetaDesa(Request $request): JsonResponse
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $data = $request->only(['nama', 'kategori', 'lat', 'lng', 'deskripsi', 'alamat', 'warna']);
        $data['lat'] = $request->input('lat', $request->input('latitude', 0));
        $data['lng'] = $request->input('lng', $request->input('longitude', 0));
        $data['kategori'] = $data['kategori'] ?? 'umum';
        $data['aktif'] = $request->boolean('aktif', true);
        $item = \App\Models\Cms\CmsPetaDesa::create($data);
        $this->audit('create_cms', 'CmsPetaDesa', (string) $item->id);
        return $this->created(new CmsPetaDesaResource($item));
    }

    public function updatePetaDesa(Request $request, $id): JsonResponse
    {
        $item = \App\Models\Cms\CmsPetaDesa::find($id);
        if (!$item) return $this->notFound('Data tidak ditemukan.');
        $data = $request->only(['nama', 'kategori', 'lat', 'lng', 'deskripsi', 'alamat', 'aktif', 'warna']);
        if ($request->has('latitude')) $data['lat'] = $request->input('latitude');
        if ($request->has('longitude')) $data['lng'] = $request->input('longitude');
        $item->update($data);
        $this->audit('update_cms', 'CmsPetaDesa', (string) $item->id);
        return $this->success(new CmsPetaDesaResource($item->fresh()));
    }

    public function destroyPetaDesa($id): JsonResponse
    {
        $item = \App\Models\Cms\CmsPetaDesa::find($id);
        if (!$item) return $this->notFound('Data tidak ditemukan.');
        $this->audit('delete_cms', 'CmsPetaDesa', (string) $item->id);
        $item->delete();
        return $this->success(null, 'Data berhasil dihapus.');
    }
}
