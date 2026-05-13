<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsPpidDokumen extends Model
{
    protected $table = 'cms_ppid_dokumen';
    protected $guarded = ['id'];
    protected function casts(): array { return ['tanggal' => 'date', 'aktif' => 'boolean']; }
}
