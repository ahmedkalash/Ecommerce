<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    use PreventDemoModeChanges;

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
