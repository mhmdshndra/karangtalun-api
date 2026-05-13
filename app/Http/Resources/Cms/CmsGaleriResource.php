<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsGaleriResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'judul' => $this->judul,
            'url' => $this->url,
            'kategori' => $this->kategori,
            'tanggal' => $this->tanggal,
            'deskripsi' => $this->deskripsi,
            'urutan' => $this->urutan,
            'aktif' => (bool) $this->aktif,
        ];
    }
}
