<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class SizeChartDetail extends Model
{
    use PreventDemoModeChanges;

    protected $guarded = [];
}
