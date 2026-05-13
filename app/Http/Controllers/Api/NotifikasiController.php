<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotifikasiResource;
use App\Models\Notifikasi;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifikasi = Notifikasi::forUser($user)
            ->orderByDesc('tanggal')
            ->paginate($request->per_page ?? 20);

        $unread = Notifikasi::forUser($user)->unread()->count();

        $data = $this->paginated($notifikasi, 'Berhasil.', NotifikasiResource::class);

        // Inject unread_count into existing response
        $response = json_decode($data->getContent(), true);
        $response['unread_count'] = $unread;

        return response()->json($response);
    }

    public function markRead(Request $request, $id): JsonResponse
    {
        $notifikasi = Notifikasi::forUser($request->user())->find($id);
        if (!$notifikasi) return $this->notFound('Notifikasi tidak ditemukan.');

        $notifikasi->update(['dibaca' => true]);
        return $this->success(null, 'Notifikasi ditandai dibaca.');
    }

    public function markAllRead(Request $request): JsonResponse
    {
        Notifikasi::forUser($request->user())
            ->unread()
            ->update(['dibaca' => true]);

        return $this->success(null, 'Semua notifikasi ditandai dibaca.');
    }
}
