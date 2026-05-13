<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsHeaderFooter extends Model
{
    protected $table = 'cms_header_footer';
    protected $guarded = ['id'];
    protected function casts(): array { return ['menu_navigasi' => 'array', 'link_sosmed' => 'array']; }
}
