<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'namaLengkap'     => $this->nama_lengkap,
            'nik'             => $this->nik,
            'noKk'            => $this->no_kk,
            'role'            => $this->role,
            'idPetugas'       => $this->id_petugas,
            'email'           => $this->email,
            'telepon'         => $this->telepon,
            'alamat'          => $this->alamat,
            'rtRw'            => $this->rt_rw,
            'foto'            => $this->foto,
            'statusAktivasi'  => $this->status_aktivasi,
            'tanggalAktivasi' => $this->tanggal_aktivasi?->toDateString(),

            // Legacy snake_case aliases for frontend compatibility
            'nama_lengkap'     => $this->nama_lengkap,
            'no_kk'            => $this->no_kk,
            'id_petugas'       => $this->id_petugas,
            'rt_rw'            => $this->rt_rw,
            'status_aktivasi'  => $this->status_aktivasi,
            'tanggal_aktivasi' => $this->tanggal_aktivasi?->toDateString(),
        ];
    }
}
