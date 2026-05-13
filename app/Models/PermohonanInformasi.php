<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanInformasi extends Model
{
    protected $table = 'permohonan_informasi';

    protected $fillable = [
        'nomor_permohonan', 'nama_pemohon', 'alamat_pemohon', 'kontak_pemohon',
        'tujuan_permohonan', 'informasi_diminta',
        'status', 'catatan_admin', 'pemohon_user_id', 'pemohon_nik',
    ];

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'pemohon_user_id');
    }

    public function lampiran()
    {
        return $this->hasMany(PermohonanLampiran::class, 'permohonan_informasi_id');
    }

    public function lampiranPemohon()
    {
        return $this->lampiran()->where('tipe', 'lampiran_pemohon');
    }

    public function balasanAdmin()
    {
        return $this->lampiran()->where('tipe', 'balasan_admin');
    }
}
