<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsPetaDesaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'lat' => (float) $this->lat,
            'lng' => (float) $this->lng,
            'deskripsi' => $this->deskripsi,
            'alamat' => $this->alamat,
            'aktif' => (bool) $this->aktif,
            'warna' => $this->warna,
        ];
    }
}
