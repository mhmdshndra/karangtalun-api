<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsHeaderFooterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'menuNavigasi' => $this->menu_navigasi ?? [],
            'teksFooter' => $this->teks_footer,
            'kontakFooter' => $this->kontak_footer,
            'jamPelayanan' => $this->jam_pelayanan,
            'linkSosmed' => $this->link_sosmed ?? [],
            'tombolWa' => $this->tombol_wa,
        ];
    }
}
