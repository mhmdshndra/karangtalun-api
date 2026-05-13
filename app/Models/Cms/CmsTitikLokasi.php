<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsTitikLokasi extends Model
{
    protected $table = 'cms_titik_lokasi';
    protected $guarded = ['id'];

    public function fasilitas()
    {
        return $this->belongsTo(CmsFasilitas::class, 'cms_fasilitas_id');
    }
}
