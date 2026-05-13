<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsPotensiDesaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'gambar' => $this->gambar,
            'urutan' => $this->urutan,
            'aktif' => (bool) $this->aktif,
        ];
    }
}
