<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratLampiran extends Model
{
    protected $table = 'surat_lampiran';

    protected $fillable = ['pengajuan_surat_id', 'filename', 'path', 'mime_type', 'size'];

    public function pengajuanSurat()
    {
        return $this->belongsTo(PengajuanSurat::class, 'pengajuan_surat_id');
    }
}
