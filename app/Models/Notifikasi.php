<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'tipe', 'judul', 'pesan', 'tanggal', 'dibaca',
        'link', 'target_role', 'target_user_id', 'target_nik',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
            'dibaca' => 'boolean',
        ];
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('target_user_id', $user->id)
              ->orWhere('target_nik', $user->nik)
              ->orWhere('target_role', $user->role)
              ->orWhere('target_role', 'all');
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('dibaca', false);
    }
}
