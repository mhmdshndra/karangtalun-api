<?php

namespace App\Http\Middleware;

use App\Services\TurnstileVerifier;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyTurnstile
{
    public function __construct(protected TurnstileVerifier $verifier) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!config('services.turnstile.enabled')) {
            return $next($request);
        }

        // Check if secret_key is configured properly
        $secretKey = config('services.turnstile.secret_key');
        if (empty($secretKey) || str_starts_with($secretKey, '0x_your_') || $secretKey === 'placeholder') {
            Log::critical('Turnstile: TURNSTILE_SECRET_KEY is not configured. Set a valid key or disable Turnstile with TURNSTILE_ENABLED=false.', [
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'success'    => false,
                'message'    => 'Konfigurasi keamanan server belum lengkap. Hubungi administrator.',
                'error_code' => 'turnstile_config_error',
            ], 500);
        }

        $token = $request->input('cf_turnstile_token');

        if (empty($token)) {
            Log::warning('Turnstile: token missing', [
                'ip'         => $request->ip(),
                'endpoint'   => $request->path(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success'    => false,
                'message'    => 'Verifikasi keamanan gagal. Silakan coba lagi.',
                'error_code' => 'turnstile_failed',
                'errors'     => [
                    'cf_turnstile_token' => ['Token verifikasi tidak valid atau sudah kadaluarsa.'],
                ],
            ], 422);
        }

        if (!$this->verifier->verify($token, $request->ip())) {
            Log::warning('Turnstile: verification failed', [
                'ip'         => $request->ip(),
                'endpoint'   => $request->path(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success'    => false,
                'message'    => 'Verifikasi keamanan gagal. Silakan coba lagi.',
                'error_code' => 'turnstile_failed',
                'errors'     => [
                    'cf_turnstile_token' => ['Token verifikasi tidak valid atau sudah kadaluarsa.'],
                ],
            ], 422);
        }

        return $next($request);
    }
}
