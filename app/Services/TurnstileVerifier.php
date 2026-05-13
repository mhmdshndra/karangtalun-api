<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileVerifier
{
    public function verify(string $token, ?string $remoteIp = null): bool
    {
        if (!config('services.turnstile.enabled')) {
            return true;
        }

        try {
            $payload = [
                'secret'   => config('services.turnstile.secret_key'),
                'response' => $token,
            ];

            if ($remoteIp) {
                $payload['remoteip'] = $remoteIp;
            }

            $response = Http::asForm()
                ->timeout(10)
                ->post(config('services.turnstile.verify_url'), $payload);

            $result = $response->json();

            if (!($result['success'] ?? false)) {
                Log::warning('Turnstile verification failed', [
                    'error_codes' => $result['error-codes'] ?? [],
                    'ip'          => $remoteIp,
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Turnstile verification error', [
                'message' => $e->getMessage(),
                'ip'      => $remoteIp,
            ]);
            return false;
        }
    }
}
