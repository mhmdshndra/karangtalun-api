<?php
namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Model;

class CmsInfografis extends Model
{
    protected $table = 'cms_infografis';
    protected $guarded = ['id'];
    protected function casts(): array { return ['data_bansos' => 'array', 'sdgs_capaian' => 'array']; }
}
