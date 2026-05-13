<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotifikasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'tipe'           => $this->tipe,
            'judul'          => $this->judul,
            'pesan'          => $this->pesan,
            'tanggal'        => $this->tanggal?->toIso8601String(),
            'dibaca'         => (bool) $this->dibaca,
            'link'           => $this->link,
            'target_role'    => $this->target_role,
            'target_nik'     => $this->target_nik,
            'target_user_id' => $this->target_user_id,
        ];
    }
}
