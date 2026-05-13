<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsPotensiDesa extends Model
{
    protected $table = 'cms_potensi_desa';
    protected $guarded = ['id'];
    protected function casts(): array { return ['aktif' => 'boolean']; }
}
