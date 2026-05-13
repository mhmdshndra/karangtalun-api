<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'turnstile' => \App\Http\Middleware\VerifyTurnstile::class,
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Consistent JSON error responses for API
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Autentikasi diperlukan. Silakan login terlebih dahulu.',
                ], 401);
            }
        });

        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengakses fitur ini.',
                ], 403);
            }
        });

        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint tidak ditemukan.',
                ], 404);
            }
        });

        $exceptions->renderable(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
                ], 429);
            }
        });
    })
    ->booting(function () {
        // ─── Rate Limiters ───────────────────────────────────────
        RateLimiter::for('login', fn(Request $r) => Limit::perMinute(5)->by($r->ip()));
        RateLimiter::for('register', fn(Request $r) => Limit::perMinute(3)->by($r->ip()));
        RateLimiter::for('laporan', fn(Request $r) => Limit::perHour(5)->by($r->ip()));
        RateLimiter::for('permohonan', fn(Request $r) => Limit::perHour(3)->by($r->ip()));
        RateLimiter::for('like_umkm', fn(Request $r) => Limit::perHour(10)->by($r->ip()));
        RateLimiter::for('view_berita', fn(Request $r) => Limit::perHour(30)->by($r->ip()));
        RateLimiter::for('api', fn(Request $r) => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
    })
    ->create();
