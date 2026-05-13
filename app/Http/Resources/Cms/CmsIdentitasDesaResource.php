<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsIdentitasDesaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'namaDesa' => $this->nama_desa,
            'kodeDesa' => $this->kode_desa,
            'kecamatan' => $this->kecamatan,
            'kabupaten' => $this->kabupaten,
            'provinsi' => $this->provinsi,
            'kodePos' => $this->kode_pos,
            'alamat' => $this->alamat,
            'email' => $this->email,
            'telepon' => $this->telepon,
            'mapsUrl' => $this->maps_url,
            'koordinat' => [
                'lat' => (float) $this->koordinat_lat,
                'lng' => (float) $this->koordinat_lng,
            ],
            'namaKades' => $this->nama_kades,
            'jabatanKades' => $this->jabatan_kades,
            'tahunAnggaran' => $this->tahun_anggaran,
            'sosialMedia' => [
                'facebook' => $this->sosmed_facebook,
                'instagram' => $this->sosmed_instagram,
                'twitter' => $this->sosmed_twitter,
                'youtube' => $this->sosmed_youtube,
                'tiktok' => $this->sosmed_tiktok,
            ],
        ];
    }
}
