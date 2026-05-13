<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsUmkm extends Model
{
    protected $table = 'cms_umkm';
    protected $guarded = ['id'];
    protected function casts(): array { return ['aktif' => 'boolean', 'unggulan' => 'boolean']; }
}
