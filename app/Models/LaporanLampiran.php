<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanLampiran extends Model
{
    protected $table = 'laporan_lampiran';

    protected $fillable = ['laporan_aduan_id', 'filename', 'path', 'mime_type', 'size'];

    public function laporanAduan()
    {
        return $this->belongsTo(LaporanAduan::class, 'laporan_aduan_id');
    }
}
