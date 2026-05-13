<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermohonanInformasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => (string) $this->id,
            'nomor_permohonan'    => $this->nomor_permohonan,
            'nama_pemohon'        => $this->nama_pemohon,
            'alamat_pemohon'      => $this->alamat_pemohon,
            'kontak_pemohon'      => $this->kontak_pemohon,
            'tujuan_permohonan'   => $this->tujuan_permohonan,
            'informasi_diminta'   => $this->informasi_diminta,
            'tanggal_permohonan'  => $this->created_at?->toDateString(),
            'tanggal_diperbarui'  => $this->updated_at?->toDateString(),
            'status'              => $this->status,
            'catatan_admin'       => $this->catatan_admin,
            'pemohon_user_id'     => $this->pemohon_user_id ? (string) $this->pemohon_user_id : null,
            'pemohon_nik'         => $this->pemohon_nik,

            'lampiran' => $this->whenLoaded('lampiran', function () {
                return $this->lampiran
                    ->where('tipe', '!=', 'balasan_admin')
                    ->map(fn($l) => $l->path)
                    ->values()
                    ->toArray();
            }, $this->relationLoaded('lampiran')
                ? $this->lampiran->where('tipe', '!=', 'balasan_admin')->map(fn($l) => $l->path)->values()->toArray()
                : []),

            'file_balasan' => $this->whenLoaded('lampiran', function () {
                return $this->lampiran
                    ->where('tipe', 'balasan_admin')
                    ->map(fn($l) => $l->path)
                    ->values()
                    ->toArray();
            }, $this->relationLoaded('lampiran')
                ? $this->lampiran->where('tipe', 'balasan_admin')->map(fn($l) => $l->path)->values()->toArray()
                : []),
        ];
    }
}
