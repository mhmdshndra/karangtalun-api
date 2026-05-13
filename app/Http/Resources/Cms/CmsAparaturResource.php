<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsAparaturResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'nama' => $this->nama,
            'jabatan' => $this->jabatan,
            'foto' => $this->foto,
            'kategoriJabatan' => $this->kategori_jabatan,
            'urutan' => $this->urutan,
            'aktif' => (bool) $this->aktif,
        ];
    }
}
