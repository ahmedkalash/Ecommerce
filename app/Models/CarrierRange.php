<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrierRange extends Model
{
    use HasFactory, PreventDemoModeChanges;

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function carrier_range_prices()
    {
        return $this->hasMany(CarrierRangePrice::class);
    }
}
