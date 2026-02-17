<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class AreaTranslation extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['name', 'lang', 'area_id'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
