<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LupaSandiIdentifyRequest;
use App\Http\Requests\Auth\LupaSandiResetRequest;
use App\Http\Resources\UserResource;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Services\SmsService;
use App\Traits\ApiResponse;
use App\Traits\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponse, AuditLogger;

    public function login(LoginRequest $request): JsonResponse
    {
        $identifier = $request->input('identifier');
        $password = $request->input('password');

        $user = User::where('nik', $identifier)->first()
             ?? User::where('id_petugas', $identifier)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            $this->audit('login_failed', 'User', null, null, ['identifier' => $identifier]);
            return $this->error('Identifier atau password salah.', 401);
        }

        if ($user->status_aktivasi !== 'aktif') {
            return $this->error('Akun belum diaktivasi atau dinonaktifkan.', 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        $this->audit('login_success', 'User', (string) $user->id);

        return $this->success([
            'user' => self::formatUser($user),
            'token' => $token,
        ], 'Login berhasil.');
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::where('nik', $request->nik)->where('role', 'warga')->first();

        if (!$user) {
            return $this->error('NIK tidak ditemukan di sistem. Hubungi Admin Desa untuk mendaftarkan data Anda terlebih dahulu.', 404);
        }

        if ($user->status_aktivasi === 'aktif') {
            return $this->error('Akun dengan NIK ini sudah aktif. Silakan login atau gunakan fitur lupa sandi.', 409);
        }

        if (mb_strtolower(trim($request->nama_lengkap)) !== mb_strtolower(trim($user->nama_lengkap))) {
            return $this->error('Nama tidak cocok dengan data yang terdaftar di sistem. Pastikan nama sesuai KTP.', 422);
        }

        $user->update([
            'status_aktivasi' => 'aktif',
            'password' => Hash::make($request->password),
            'telepon' => $request->telepon,
            'tanggal_aktivasi' => now()->toDateString(),
        ]);

        $this->audit('register', 'User', (string) $user->id);
        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->created([
            'user' => self::formatUser($user->fresh()),
            'token' => $token,
        ], 'Aktivasi akun berhasil.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logout berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(self::formatUser($request->user()));
    }

    // ── Step A: Identify user → generate & send OTP ──────────────
    public function lupaSandiIdentify(LupaSandiIdentifyRequest $request): JsonResponse
    {
        $identifier = $request->input('identifier', $request->input('nik'));

        // Support lookup by NIK or id_petugas
        $user = User::where('nik', $identifier)->where('status_aktivasi', 'aktif')->first()
             ?? User::where('id_petugas', $identifier)->where('status_aktivasi', 'aktif')->first();

        if (!$user) {
            return $this->error('Identifier tidak ditemukan atau akun belum aktif.', 404);
        }

        // Invalidate existing OTPs for this user
        PasswordResetOtp::where('identifier', $user->nik)->delete();

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store hashed OTP
        PasswordResetOtp::create([
            'identifier' => $user->nik,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes((int) config('services.otp.expiry_minutes', 10)),
            'attempts' => 0,
        ]);

        // Send OTP via configured channel
        $phone = $user->telepon;
        $sent = false;

        if ($phone) {
            $sent = SmsService::sendOtp($phone, $otp);
        }

        // In production, fail if OTP could not be sent (no phone or provider error)
        if (!$sent && !app()->environment('local', 'testing')) {
            // Clean up the OTP record since it can't be delivered
            PasswordResetOtp::where('identifier', $user->nik)->delete();
            return $this->error('Gagal mengirim OTP. Pastikan nomor telepon terdaftar dan coba lagi.', 500);
        }

        $maskedPhone = $phone
            ? substr($phone, 0, 4) . '****' . substr($phone, -3)
            : null;

        $this->audit('lupa_sandi_identify', 'User', (string) $user->id);

        $responseData = [
            'nik' => $user->nik,
            'nama' => $user->nama_lengkap,
            'telepon_masked' => $maskedPhone,
        ];

        // Only include OTP in response for local/testing when OTP_DEBUG=true
        if (config('services.otp.debug') && app()->environment('local', 'testing')) {
            $responseData['debug_otp'] = $otp;
        }

        return $this->success($responseData, 'Kode OTP telah dikirim. Silakan cek HP Anda.');
    }

    // ── Step B: Verify OTP → return reset_token ──────────────────
    public function lupaSandiVerifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        // Resolve identifier to NIK (OTP records keyed by NIK)
        $identifier = $this->resolveIdentifierToNik($request->input('identifier'));

        $otpRecord = PasswordResetOtp::where('identifier', $identifier)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return $this->error('Tidak ada permintaan OTP yang aktif.', 422);
        }

        if ($otpRecord->isExpired()) {
            $otpRecord->delete();
            return $this->error('Kode OTP sudah kadaluarsa. Silakan minta OTP baru.', 422);
        }

        if ($otpRecord->hasExceededAttempts()) {
            $otpRecord->delete();
            return $this->error('Terlalu banyak percobaan. Silakan minta OTP baru.', 429);
        }

        if (!Hash::check($request->otp, $otpRecord->otp_hash)) {
            $otpRecord->increment('attempts');
            $remaining = 5 - $otpRecord->fresh()->attempts;
            return $this->error("Kode OTP salah. Sisa percobaan: {$remaining}.", 422);
        }

        // OTP valid → generate reset token
        $resetToken = Str::random(64);
        $otpRecord->update([
            'used_at' => now(),
            'reset_token_hash' => Hash::make($resetToken),
            'reset_token_expires_at' => now()->addMinutes(15),
        ]);

        return $this->success([
            'reset_token' => $resetToken,
        ], 'Verifikasi OTP berhasil.');
    }

    // ── Legacy verify-hp (backward compat) ───────────────────────
    public function lupaSandiVerifyHp(Request $request): JsonResponse
    {
        return $this->lupaSandiVerifyOtp($request);
    }

    // ── Step C: Reset password with reset_token ──────────────────
    public function lupaSandiReset(LupaSandiResetRequest $request): JsonResponse
    {
        $identifier = $this->resolveIdentifierToNik(
            $request->input('identifier', $request->input('nik'))
        );

        $otpRecord = PasswordResetOtp::where('identifier', $identifier)
            ->whereNotNull('reset_token_hash')
            ->whereNotNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return $this->error('Token reset tidak valid.', 422);
        }

        $token = $request->input('reset_token', $request->input('token'));

        if (!Hash::check($token, $otpRecord->reset_token_hash)) {
            return $this->error('Token reset tidak valid.', 422);
        }

        if ($otpRecord->reset_token_expires_at && $otpRecord->reset_token_expires_at->isPast()) {
            $otpRecord->delete();
            return $this->error('Token reset sudah kadaluarsa.', 422);
        }

        $user = User::where('nik', $identifier)->first();
        if (!$user) {
            return $this->error('User tidak ditemukan.', 404);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Invalidate all OTPs and tokens for this user
        PasswordResetOtp::where('identifier', $identifier)->delete();

        // Revoke all Sanctum tokens
        $user->tokens()->delete();

        $this->audit('password_reset', 'User', (string) $user->id);

        return $this->success(null, 'Password berhasil direset. Silakan login dengan password baru.');
    }

    public static function formatUser(User $user): array
    {
        return (new UserResource($user))->resolve();
    }

    /**
     * Resolve an identifier (NIK or id_petugas) to NIK.
     * OTP records are always keyed by NIK.
     */
    private function resolveIdentifierToNik(string $identifier): string
    {
        // If it looks like a petugas ID (e.g. ADM-001, STF-001), resolve to NIK
        $user = User::where('id_petugas', $identifier)->first();
        if ($user) {
            return $user->nik;
        }
        return $identifier;
    }
}
