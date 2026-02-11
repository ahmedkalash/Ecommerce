<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use PreventDemoModeChanges;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRoleAttribute()
    {
        // Proxy to the user's first role (since we enforce single role logic in controller)
        return $this->user->roles->first();
    }

    public function pick_up_point()
    {
        return $this->hasOne(PickupPoint::class);
    }
}
