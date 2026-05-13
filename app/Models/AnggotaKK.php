<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggotaKK extends Model
{
    protected $table = 'anggota_kk';

    protected $fillable = [
        'no_kk', 'nik', 'nama_lengkap', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'agama', 'pendidikan',
        'pekerjaan', 'status_perkawinan', 'status_hubungan', 'kewarganegaraan',
    ];

    protected function casts(): array
    {
        return ['tanggal_lahir' => 'date'];
    }

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'no_kk', 'no_kk');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'nik', 'nik');
    }

    public function getUmurAttribute(): int
    {
        return $this->tanggal_lahir->age;
    }
}
