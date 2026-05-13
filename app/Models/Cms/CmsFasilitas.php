<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsFasilitas extends Model
{
    protected $table = 'cms_fasilitas';
    protected $guarded = ['id'];
    protected function casts(): array { return ['aktif' => 'boolean']; }

    public function titikLokasi()
    {
        return $this->hasMany(CmsTitikLokasi::class, 'cms_fasilitas_id');
    }
}
