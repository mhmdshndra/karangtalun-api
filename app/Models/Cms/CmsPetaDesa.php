<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsPetaDesa extends Model
{
    protected $table = 'cms_peta_desa';
    protected $guarded = ['id'];
    protected function casts(): array { return ['aktif' => 'boolean']; }
}
