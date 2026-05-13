<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanSurat extends Model
{
    protected $table = 'pengajuan_surat';

    protected $fillable = [
        'jenis_surat', 'nomor_tiket', 'nomor_surat', 'status',
        'tanggal_pengajuan', 'tanggal_diperbarui',
        'diajukan_oleh_user_id', 'diajukan_oleh_nik', 'diajukan_oleh_nama',
        'pemohon_nik', 'pemohon_nama_lengkap', 'pemohon_tempat_lahir',
        'pemohon_tanggal_lahir', 'pemohon_jenis_kelamin', 'pemohon_pekerjaan',
        'pemohon_alamat', 'pemohon_status_hubungan',
        'keperluan', 'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'tanggal_diperbarui' => 'date',
            'pemohon_tanggal_lahir' => 'date',
        ];
    }

    public function diajukanOleh()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh_user_id');
    }

    public function lampiran()
    {
        return $this->hasMany(SuratLampiran::class, 'pengajuan_surat_id');
    }
}
