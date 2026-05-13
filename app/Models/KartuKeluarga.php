<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuKeluarga extends Model
{
    protected $table = 'kartu_keluarga';
    protected $primaryKey = 'no_kk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_kk', 'kepala_keluarga', 'alamat', 'rt_rw',
        'kelurahan', 'kecamatan', 'kabupaten', 'provinsi',
    ];

    public function anggota()
    {
        return $this->hasMany(AnggotaKK::class, 'no_kk', 'no_kk');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'no_kk', 'no_kk');
    }
}
