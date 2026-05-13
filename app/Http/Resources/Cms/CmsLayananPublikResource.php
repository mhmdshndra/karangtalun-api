<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsLayananPublikResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'kategori' => $this->kategori,
            'estimasiWaktu' => $this->estimasi_waktu,
            'biaya' => $this->biaya,
            'persyaratan' => $this->persyaratan ?? [],
            'prosedur' => $this->prosedur ?? [],
            'aktif' => (bool) $this->aktif,
            'butuhLogin' => (bool) $this->butuh_login,
            'instruksi' => $this->instruksi,
            'routeSlug' => $this->route_slug,
            'tipeLayanan' => $this->tipe_layanan,
        ];
    }
}
