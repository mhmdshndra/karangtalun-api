<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsPpidDokumenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'judul' => $this->judul,
            'kategori' => $this->kategori,
            'tanggal' => $this->tanggal instanceof \DateTimeInterface
                ? $this->tanggal->format('Y-m-d')
                : $this->tanggal,
            'fileUrl' => $this->file_url,
            'aktif' => (bool) $this->aktif,
            'urutan' => $this->urutan,
        ];
    }
}
