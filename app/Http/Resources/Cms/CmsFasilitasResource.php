<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsFasilitasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'gambar' => $this->gambar,
            'label' => $this->label,
            'urutan' => $this->urutan,
            'aktif' => (bool) $this->aktif,
            'titikLokasi' => CmsTitikLokasiResource::collection($this->whenLoaded('titikLokasi', $this->titikLokasi, collect())),
        ];
    }
}
