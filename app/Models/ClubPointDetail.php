<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class ClubPointDetail extends Model
{
    use PreventDemoModeChanges;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function club_point()
    {
        return $this->belongsTo(ClubPoint::class);
    }
}
