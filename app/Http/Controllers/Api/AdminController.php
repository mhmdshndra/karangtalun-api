<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KartuKeluarga;
use App\Models\AnggotaKK;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use ApiResponse, AuditLogger;

    // ── KK ─────────────────────────────────────────
    public function indexKK(Request $request): JsonResponse
    {
        $query = KartuKeluarga::with('anggota')
            ->when($request->search, fn($q, $s) => $q->where(function ($q2) use ($s) {
                $q2->where('no_kk', 'like', "%{$s}%")
                   ->orWhere('kepala_keluarga', 'like', "%{$s}%");
            }))
            ->when($request->rt_rw, fn($q, $r) => $q->where('rt_rw', $r))
            ->orderByDesc('created_at');

        return $this->paginated($query->paginate($request->per_page ?? 15));
    }

    public function storeKK(Request $request): JsonResponse
    {
        $request->validate([
            'no_kk' => 'required|string|size:16|unique:kartu_keluarga,no_kk',
            'kepala_keluarga' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'rt_rw' => 'required|string|max:20',
            'kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kabupaten' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'anggota' => 'nullable|array',
            'anggota.*.nik' => 'required|string|size:16|unique:anggota_kk,nik',
            'anggota.*.nama_lengkap' => 'required|string|max:255',
            'anggota.*.jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'anggota.*.tempat_lahir' => 'required|string|max:255',
            'anggota.*.tanggal_lahir' => 'required|date',
            'anggota.*.agama' => 'required|string',
            'anggota.*.pendidikan' => 'required|string',
            'anggota.*.pekerjaan' => 'required|string|max:255',
            'anggota.*.status_perkawinan' => 'required|string',
            'anggota.*.status_hubungan' => 'required|string',
        ]);

        return DB::transaction(function () use ($request) {
            $kk = KartuKeluarga::create($request->only([
                'no_kk', 'kepala_keluarga', 'alamat', 'rt_rw',
                'kelurahan', 'kecamatan', 'kabupaten', 'provinsi',
            ]));

            if ($request->has('anggota')) {
                foreach ($request->anggota as $a) {
                    $a['no_kk'] = $kk->no_kk;
                    $a['kewarganegaraan'] = $a['kewarganegaraan'] ?? 'WNI';
                    AnggotaKK::create($a);
                }
            }

            $this->audit('create_kk', 'KartuKeluarga', $kk->no_kk);
            return $this->created($kk->load('anggota'), 'KK berhasil ditambahkan.');
        });
    }

    public function updateKK(Request $request, $no_kk): JsonResponse
    {
        $kk = KartuKeluarga::find($no_kk);
        if (!$kk) return $this->notFound('KK tidak ditemukan.');

        $old = $kk->toArray();
        $kk->update($request->only([
            'kepala_keluarga', 'alamat', 'rt_rw',
            'kelurahan', 'kecamatan', 'kabupaten', 'provinsi',
        ]));

        $this->audit('update_kk', 'KartuKeluarga', $kk->no_kk, $old, $kk->toArray());
        return $this->success($kk->fresh()->load('anggota'), 'KK berhasil diperbarui.');
    }

    public function destroyKK($no_kk): JsonResponse
    {
        $kk = KartuKeluarga::find($no_kk);
        if (!$kk) return $this->notFound('KK tidak ditemukan.');

        $this->audit('delete_kk', 'KartuKeluarga', $kk->no_kk);
        $kk->delete();
        return $this->success(null, 'KK berhasil dihapus.');
    }

    public function storeAnggota(Request $request, $no_kk): JsonResponse
    {
        $kk = KartuKeluarga::find($no_kk);
        if (!$kk) return $this->notFound('KK tidak ditemukan.');

        $request->validate([
            'nik' => 'required|string|size:16|unique:anggota_kk,nik',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string',
            'pendidikan' => 'required|string',
            'pekerjaan' => 'required|string',
            'status_perkawinan' => 'required|string',
            'status_hubungan' => 'required|string',
        ]);

        $data = $request->only([
            'nik', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
            'agama', 'pendidikan', 'pekerjaan', 'status_perkawinan', 'status_hubungan',
        ]);
        $data['no_kk'] = $no_kk;
        $data['kewarganegaraan'] = $request->input('kewarganegaraan', 'WNI');

        $anggota = AnggotaKK::create($data);
        $this->audit('create_anggota', 'AnggotaKK', (string) $anggota->id);

        return $this->created($anggota, 'Anggota berhasil ditambahkan.');
    }

    public function updateAnggota(Request $request, $no_kk, $nik): JsonResponse
    {
        $anggota = AnggotaKK::where('no_kk', $no_kk)->where('nik', $nik)->first();
        if (!$anggota) return $this->notFound('Anggota tidak ditemukan.');

        $old = $anggota->toArray();
        $anggota->update($request->only([
            'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
            'agama', 'pendidikan', 'pekerjaan', 'status_perkawinan', 'status_hubungan',
        ]));

        $this->audit('update_anggota', 'AnggotaKK', (string) $anggota->id, $old, $anggota->toArray());
        return $this->success($anggota->fresh(), 'Anggota berhasil diperbarui.');
    }

    public function destroyAnggota($no_kk, $nik): JsonResponse
    {
        $anggota = AnggotaKK::where('no_kk', $no_kk)->where('nik', $nik)->first();
        if (!$anggota) return $this->notFound('Anggota tidak ditemukan.');

        $this->audit('delete_anggota', 'AnggotaKK', (string) $anggota->id);
        $anggota->delete();
        return $this->success(null, 'Anggota berhasil dihapus.');
    }

    // ── Users ──────────────────────────────────────
    public function indexUsers(Request $request): JsonResponse
    {
        $query = User::query()
            ->when($request->role, fn($q, $r) => $q->where('role', $r))
            ->when($request->status, fn($q, $s) => $q->where('status_aktivasi', $s))
            ->when($request->search, fn($q, $s) => $q->where(function ($q2) use ($s) {
                $q2->where('nama_lengkap', 'like', "%{$s}%")
                   ->orWhere('nik', 'like', "%{$s}%")
                   ->orWhere('id_petugas', 'like', "%{$s}%");
            }))
            ->orderByDesc('created_at');

        return $this->paginated($query->paginate($request->per_page ?? 15));
    }

    public function createManual(Request $request): JsonResponse
    {
        $rules = [
            'nik' => 'required|string|size:16|unique:users,nik',
            'nama_lengkap' => 'required|string|max:255',
            'role' => 'required|in:warga,staf_layanan,admin_desa',
            'password' => 'required|string|min:6',
            'id_petugas' => 'nullable|string|unique:users,id_petugas',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'rt_rw' => 'nullable|string|max:20',
        ];

        // no_kk required for warga, optional for staf/admin
        if ($request->input('role') === 'warga') {
            $rules['no_kk'] = 'required|string|size:16';
        } else {
            $rules['no_kk'] = 'nullable|string|size:16';
        }

        // id_petugas required for staf/admin
        if (in_array($request->input('role'), ['staf_layanan', 'admin_desa'])) {
            $rules['id_petugas'] = 'required|string|unique:users,id_petugas';
        }

        $request->validate($rules);

        // For warga role, validate that the KK exists and NIK is registered as an anggota_kk
        if ($request->input('role') === 'warga') {
            $kk = \App\Models\KartuKeluarga::where('no_kk', $request->no_kk)->first();
            if (!$kk) {
                return $this->error('No. KK tidak ditemukan di database Kartu Keluarga. Tambahkan data KK terlebih dahulu.', 422);
            }

            $anggota = \App\Models\AnggotaKK::where('nik', $request->nik)->first();
            if (!$anggota) {
                return $this->error('NIK tidak ditemukan di data Anggota KK. Tambahkan data anggota KK terlebih dahulu.', 422);
            }

            if ($anggota->no_kk !== $request->no_kk) {
                return $this->error('NIK terdaftar pada KK lain (' . $anggota->no_kk . '). Pastikan no_kk sesuai.', 422);
            }
        }

        $user = User::create([
            ...$request->only(['nik', 'nama_lengkap', 'no_kk', 'role', 'id_petugas', 'email', 'telepon', 'alamat', 'rt_rw']),
            'password' => Hash::make($request->password),
            'status_aktivasi' => 'aktif',
            'tanggal_aktivasi' => now()->toDateString(),
        ]);

        $this->audit('create_user_manual', 'User', (string) $user->id);

        return $this->created(\App\Http\Controllers\Auth\AuthController::formatUser($user), 'Akun berhasil dibuat.');
    }

    public function syncWarga(Request $request): JsonResponse
    {
        // Single-NIK sync: POST /admin/users/sync { nik: "..." }
        if ($request->filled('nik')) {
            $nik = $request->input('nik');
            if (User::where('nik', $nik)->exists()) {
                return $this->error('Akun dengan NIK ini sudah ada.', 409);
            }
            $anggota = AnggotaKK::with('kartuKeluarga')->where('nik', $nik)->first();
            if (!$anggota) {
                return $this->notFound('Data warga dengan NIK tersebut tidak ditemukan di database KK.');
            }
            $user = User::create([
                'nama_lengkap' => $anggota->nama_lengkap,
                'nik' => $anggota->nik,
                'no_kk' => $anggota->no_kk,
                'role' => 'warga',
                'alamat' => $anggota->kartuKeluarga->alamat ?? null,
                'rt_rw' => $anggota->kartuKeluarga->rt_rw ?? null,
                'status_aktivasi' => 'belum_aktivasi',
                'password' => Hash::make('temporary'),
            ]);
            $this->audit('sync_warga', 'User', (string) $user->id);
            return $this->created(new \App\Http\Resources\UserResource($user), 'Akun warga berhasil disinkronkan.');
        }

        // Bulk sync: all unlinked anggota
        $allAnggota = AnggotaKK::with('kartuKeluarga')->get();
        $synced = 0;

        foreach ($allAnggota as $anggota) {
            if (User::where('nik', $anggota->nik)->exists()) continue;

            User::create([
                'nama_lengkap' => $anggota->nama_lengkap,
                'nik' => $anggota->nik,
                'no_kk' => $anggota->no_kk,
                'role' => 'warga',
                'alamat' => $anggota->kartuKeluarga->alamat ?? null,
                'rt_rw' => $anggota->kartuKeluarga->rt_rw ?? null,
                'status_aktivasi' => 'belum_aktivasi',
                'password' => Hash::make('temporary'),
            ]);
            $synced++;
        }

        $this->audit('sync_warga', 'User', null, null, ['synced_count' => $synced]);
        return $this->success(['synced' => $synced], "{$synced} akun warga berhasil disinkronkan.");
    }

    public function updateStaffAccount(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) return $this->notFound('User tidak ditemukan.');

        $request->validate([
            'nama_lengkap' => 'nullable|string|max:255',
            'id_petugas' => 'nullable|string|unique:users,id_petugas,' . $id,
            'email' => 'nullable|email',
            'telepon' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        $data = $request->only(['nama_lengkap', 'id_petugas', 'email', 'telepon']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $old = $user->only(array_keys($data));
        $user->update($data);
        $this->audit('update_staff_account', 'User', (string) $user->id, $old, $data);

        return $this->success(\App\Http\Controllers\Auth\AuthController::formatUser($user->fresh()), 'Akun staf berhasil diperbarui.');
    }

    public function updateUserStatus(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) return $this->notFound('User tidak ditemukan.');

        $request->validate(['status_aktivasi' => 'required|in:aktif,nonaktif']);

        $old = $user->status_aktivasi;
        $user->update(['status_aktivasi' => $request->status_aktivasi]);

        // Revoke all tokens when deactivating
        if ($request->status_aktivasi === 'nonaktif') {
            $user->tokens()->delete();
        }

        $this->audit('update_user_status', 'User', (string) $user->id,
            ['status' => $old], ['status' => $request->status_aktivasi]);

        return $this->success(\App\Http\Controllers\Auth\AuthController::formatUser($user->fresh()), 'Status user berhasil diperbarui.');
    }
}
