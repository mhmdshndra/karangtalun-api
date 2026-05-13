<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap', 'nik', 'no_kk', 'role', 'id_petugas',
        'email', 'telepon', 'alamat', 'rt_rw', 'foto',
        'status_aktivasi', 'password', 'tanggal_aktivasi',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'tanggal_aktivasi' => 'date',
        ];
    }

    // ── Relationships ──

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'no_kk', 'no_kk');
    }

    public function pengajuanSurat()
    {
        return $this->hasMany(PengajuanSurat::class, 'diajukan_oleh_user_id');
    }

    public function laporanAduan()
    {
        return $this->hasMany(LaporanAduan::class, 'pelapor_user_id');
    }

    public function permohonanInformasi()
    {
        return $this->hasMany(PermohonanInformasi::class, 'pemohon_user_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'target_user_id');
    }

    // ── Scopes ──

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktivasi', 'aktif');
    }

    // ── Helpers ──

    public function isAdmin(): bool { return $this->role === 'admin_desa'; }
    public function isStaf(): bool { return $this->role === 'staf_layanan'; }
    public function isWarga(): bool { return $this->role === 'warga'; }
    public function isStafOrAdmin(): bool { return in_array($this->role, ['admin_desa', 'staf_layanan']); }
}
