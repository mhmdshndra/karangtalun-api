<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsTitikLokasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'nama' => $this->nama,
            'label' => $this->label,
            'lat' => (float) $this->lat,
            'lng' => (float) $this->lng,
            'routeLink' => $this->route_link,
            'urutan' => $this->urutan,
        ];
    }
}
