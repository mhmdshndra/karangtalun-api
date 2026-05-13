<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsLayananPublik extends Model
{
    protected $table = 'cms_layanan_publik';
    protected $guarded = ['id'];
    protected function casts(): array { return ['persyaratan' => 'array', 'prosedur' => 'array', 'aktif' => 'boolean', 'butuh_login' => 'boolean']; }
}
