<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsGaleri extends Model
{
    protected $table = 'cms_galeri';
    protected $guarded = ['id'];
    protected function casts(): array { return ['aktif' => 'boolean']; }
}
