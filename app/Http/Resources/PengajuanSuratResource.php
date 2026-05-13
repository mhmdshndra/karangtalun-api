<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengajuanSuratResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => (string) $this->id,
            'jenis_surat'       => $this->jenis_surat,
            'jenis_surat_label' => $this->jenisSuratLabel(),
            'nomor_tiket'       => $this->nomor_tiket,
            'nomor_surat'       => $this->nomor_surat,
            'tanggal_pengajuan' => $this->tanggal_pengajuan?->toDateString(),
            'tanggal_diperbarui'=> $this->tanggal_diperbarui?->toDateString(),
            'status'            => $this->status,
            'keperluan'         => $this->keperluan,
            'catatan_admin'     => $this->catatan_admin,

            'diajukan_oleh' => [
                'nik'           => $this->diajukan_oleh_nik,
                'nama_lengkap'  => $this->diajukan_oleh_nama,
            ],

            'pemohon' => [
                'nik'              => $this->pemohon_nik,
                'nama_lengkap'     => $this->pemohon_nama_lengkap,
                'tempat_lahir'     => $this->pemohon_tempat_lahir,
                'tanggal_lahir'    => $this->pemohon_tanggal_lahir?->toDateString(),
                'jenis_kelamin'    => $this->pemohon_jenis_kelamin,
                'pekerjaan'        => $this->pemohon_pekerjaan,
                'alamat'           => $this->pemohon_alamat,
                'status_hubungan'  => $this->pemohon_status_hubungan,
            ],

            'berkas_lampiran' => $this->whenLoaded('lampiran', function () {
                return $this->lampiran->map(fn($l) => $l->path)->values()->toArray();
            }, $this->relationLoaded('lampiran')
                ? $this->lampiran->map(fn($l) => $l->path)->values()->toArray()
                : []),
        ];
    }

    private function jenisSuratLabel(): string
    {
        return match ($this->jenis_surat) {
            'surat_keterangan_domisili'    => 'Surat Keterangan Domisili',
            'surat_keterangan_tidak_mampu' => 'Surat Keterangan Tidak Mampu (SKTM)',
            'surat_pengantar_skck'         => 'Surat Pengantar SKCK',
            'surat_keterangan_usaha'       => 'Surat Keterangan Usaha',
            default => str_replace('_', ' ', ucfirst($this->jenis_surat)),
        };
    }
}
