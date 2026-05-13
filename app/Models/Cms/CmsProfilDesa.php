<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsProfilDesa extends Model
{
    protected $table = 'cms_profil_desa';
    protected $guarded = ['id'];
    protected function casts(): array { return ['misi' => 'array']; }
}
