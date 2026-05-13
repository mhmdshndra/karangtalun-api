<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanLampiran extends Model
{
    protected $table = 'permohonan_lampiran';

    protected $fillable = [
        'permohonan_informasi_id', 'filename', 'path', 'mime_type', 'size', 'tipe',
    ];

    public function permohonanInformasi()
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_informasi_id');
    }
}
