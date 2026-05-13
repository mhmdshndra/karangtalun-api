<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsBeritaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'judul' => $this->judul,
            'slug' => $this->slug,
            'kategori' => $this->kategori,
            'penulis' => $this->penulis,
            'tanggal' => $this->tanggal,
            'waktu' => $this->waktu,
            'views' => $this->views,
            'status' => $this->status,
            'tipe' => $this->tipe,
            'linkVideo' => $this->link_video,
            'thumbnail' => $this->thumbnail,
            'konten' => $this->konten,
            'isFeatured' => (bool) $this->is_featured,
        ];
    }
}
