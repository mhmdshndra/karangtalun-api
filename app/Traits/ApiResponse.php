<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data = null, string $message = 'Berhasil.', int $code = 200): JsonResponse
    {
        $response = ['success' => true, 'message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        return response()->json($response, $code);
    }

    protected function created($data = null, string $message = 'Data berhasil dibuat.'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function error(string $message = 'Terjadi kesalahan.', int $code = 400, $errors = null): JsonResponse
    {
        $response = ['success' => false, 'message' => $message];
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $code);
    }

    protected function notFound(string $message = 'Data tidak ditemukan.'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function forbidden(string $message = 'Anda tidak memiliki izin.'): JsonResponse
    {
        return $this->error($message, 403);
    }

    protected function paginated($paginator, string $message = 'Berhasil.', ?string $resourceClass = null): JsonResponse
    {
        $items = $paginator->items();
        if ($resourceClass) {
            $items = $resourceClass::collection(collect($items))->resolve();
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $items,
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }
}
