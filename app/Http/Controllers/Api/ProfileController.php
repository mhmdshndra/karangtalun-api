<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Traits\ApiResponse;
use App\Traits\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponse, AuditLogger;

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('kartuKeluarga.anggota');
        return $this->success($user);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $old = $user->only(['nama_lengkap', 'email', 'telepon', 'alamat', 'rt_rw']);
        $user->update($request->validated());
        $this->audit('update_profile', 'User', $user->id, $old, $request->validated());
        return $this->success($user->fresh(), 'Profil berhasil diperbarui.');
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Password lama tidak sesuai.', 422);
        }

        $user->update(['password' => Hash::make($request->password)]);
        $this->audit('update_password', 'User', $user->id);

        return $this->success(null, 'Password berhasil diperbarui.');
    }

    public function uploadFoto(Request $request): JsonResponse
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = $request->user();

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $path = $request->file('foto')->store('foto-profil', 'public');
        $user->update(['foto' => $path]);
        $this->audit('upload_foto', 'User', $user->id);

        return $this->success(['foto' => $path], 'Foto profil berhasil diperbarui.');
    }
}
