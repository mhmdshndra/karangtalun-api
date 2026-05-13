<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send OTP via configured channel (log, whatsapp, sms).
     *
     * HARDENING: OTP_CHANNEL=log is forbidden in production.
     * If the channel is 'log' and APP_ENV is 'production', this
     * method will return false and log a critical error.
     */
    public static function sendOtp(string $phone, string $otp): bool
    {
        $channel = config('services.otp.channel', 'log');

        // ── Production guard: disallow 'log' channel ──
        if ($channel === 'log' && app()->environment('production')) {
            Log::critical('OTP_CHANNEL=log is not allowed in production. Set OTP_CHANNEL to whatsapp or sms.');
            return false;
        }

        return match ($channel) {
            'whatsapp' => self::sendWhatsApp($phone, $otp),
            'sms'      => self::sendSms($phone, $otp),
            default    => self::sendLog($phone, $otp),
        };
    }

    protected static function sendLog(string $phone, string $otp): bool
    {
        Log::info('OTP sent via log channel', [
            'phone' => $phone,
            'otp' => $otp,
        ]);
        return true;
    }

    protected static function sendWhatsApp(string $phone, string $otp): bool
    {
        $url = config('services.otp.whatsapp_api_url');
        $token = config('services.otp.whatsapp_api_token');

        if (empty($url) || empty($token)) {
            Log::error('WhatsApp API not configured');
            return false;
        }

        try {
            $response = Http::withToken($token)
                ->timeout(15)
                ->post($url, [
                    'phone' => $phone,
                    'message' => "Kode OTP Reset Password Portal Desa Karangtalun: {$otp}\nBerlaku 10 menit. Jangan berikan kode ini kepada siapapun.",
                ]);

            if ($response->successful()) {
                Log::info('OTP sent via WhatsApp', ['phone' => $phone]);
                return true;
            }

            Log::warning('WhatsApp API failed', ['phone' => $phone, 'status' => $response->status()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp API error', ['phone' => $phone, 'error' => $e->getMessage()]);
            return false;
        }
    }

    protected static function sendSms(string $phone, string $otp): bool
    {
        $url = config('services.otp.sms_api_url');
        $token = config('services.otp.sms_api_token');

        if (empty($url) || empty($token)) {
            Log::error('SMS API not configured');
            return false;
        }

        try {
            $response = Http::withToken($token)
                ->timeout(15)
                ->post($url, [
                    'phone' => $phone,
                    'message' => "Kode OTP Reset Password: {$otp}. Berlaku 10 menit.",
                ]);

            if ($response->successful()) {
                Log::info('OTP sent via SMS', ['phone' => $phone]);
                return true;
            }

            Log::warning('SMS API failed', ['phone' => $phone, 'status' => $response->status()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('SMS API error', ['phone' => $phone, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
