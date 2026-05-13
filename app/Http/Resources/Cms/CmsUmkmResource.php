<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsUmkmResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'nama' => $this->nama,
            'slug' => $this->slug,
            'kategori' => $this->kategori,
            'namaPenjual' => $this->nama_penjual,
            'rtRw' => $this->rt_rw,
            'whatsapp' => $this->whatsapp,
            'harga' => $this->harga,
            'foto' => $this->foto,
            'deskripsi' => $this->deskripsi,
            'likes' => $this->likes,
            'aktif' => (bool) $this->aktif,
            'unggulan' => (bool) $this->unggulan,
        ];
    }
}
