<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class FollowSeller extends Model
{
    use PreventDemoModeChanges;

    protected $guarded = [];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
