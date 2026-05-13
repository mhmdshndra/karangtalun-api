<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsBerita extends Model
{
    protected $table = 'cms_berita';
    protected $guarded = ['id'];
    protected function casts(): array { return ['is_featured' => 'boolean']; }

    public function scopeTerbit($q) { return $q->where('status', 'Terbit'); }
}
