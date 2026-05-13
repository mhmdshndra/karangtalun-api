<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsAparatur extends Model
{
    protected $table = 'cms_aparatur';
    protected $guarded = ['id'];
    protected function casts(): array { return ['aktif' => 'boolean']; }
}
