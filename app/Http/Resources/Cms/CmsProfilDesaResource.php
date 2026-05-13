<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsProfilDesaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sejarah' => $this->sejarah,
            'visi' => $this->visi,
            'misi' => $this->misi ?? [],
            'potensi' => $this->potensi,
            'sambutan' => $this->sambutan,
            'fotoKades' => $this->foto_kades,
            'strukturPemerintahan' => $this->struktur_pemerintahan,
            'fasilitas' => $this->fasilitas_teks,
        ];
    }
}
