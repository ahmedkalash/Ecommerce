<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class SizeChart extends Model
{
    use PreventDemoModeChanges;

    protected $guarded = [];

    public function sizeChartDetails()
    {
        return $this->hasMany(SizeChartDetail::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
