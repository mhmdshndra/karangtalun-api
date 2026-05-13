<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsInfografisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'jumlahPenduduk' => $this->additional['jumlah_penduduk'] ?? 0,
            'jumlahKK' => $this->additional['jumlah_kk'] ?? 0,
            'apbdesTotal' => $this->apbdes_total,
            'apbdesRealisasi' => $this->apbdes_realisasi,
            'idmSkor' => (float) $this->idm_skor,
            'idmStatus' => $this->idm_status,
            'stuntingTotal' => $this->stunting_total,
            'stuntingKasus' => $this->stunting_kasus,
            'dataBansos' => $this->data_bansos ?? [],
            'sdgsCapaian' => $this->sdgs_capaian ?? [],
        ];
    }
}
