<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class CityTranslation extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['name', 'lang', 'city_id'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
