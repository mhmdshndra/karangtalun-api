<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaporanAduanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => (string) $this->id,
            'nomor_tiket'       => $this->nomor_tiket,
            'kategori'          => $this->kategori,
            'nama_pelapor'      => $this->nama_pelapor,
            'alamat_pelapor'    => $this->alamat_pelapor,
            'kontak_pelapor'    => $this->kontak_pelapor,
            'deskripsi'         => $this->deskripsi,
            'lokasi_kejadian'   => $this->lokasi_kejadian,
            'lokasi_gps'        => $this->lokasi_gps,
            'tanggal_laporan'   => $this->created_at?->toDateString(),
            'tanggal_diperbarui'=> $this->updated_at?->toDateString(),
            'status'            => $this->status,
            'catatan_admin'     => $this->catatan_admin,
            'pelapor_user_id'   => $this->pelapor_user_id ? (string) $this->pelapor_user_id : null,
            'pelapor_nik'       => $this->pelapor_nik,

            'lampiran' => $this->whenLoaded('lampiran', function () {
                return $this->lampiran->map(fn($l) => $l->path)->values()->toArray();
            }, $this->relationLoaded('lampiran')
                ? $this->lampiran->map(fn($l) => $l->path)->values()->toArray()
                : []),
        ];
    }
}
