<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanAduan extends Model
{
    protected $table = 'laporan_aduan';

    protected $fillable = [
        'nomor_tiket', 'kategori', 'nama_pelapor', 'alamat_pelapor',
        'kontak_pelapor', 'deskripsi', 'lokasi_kejadian', 'lokasi_gps',
        'status', 'catatan_admin', 'pelapor_user_id', 'pelapor_nik',
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_user_id');
    }

    public function lampiran()
    {
        return $this->hasMany(LaporanLampiran::class, 'laporan_aduan_id');
    }
}
