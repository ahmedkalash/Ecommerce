<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrierRangePrice extends Model
{
    use HasFactory, PreventDemoModeChanges;

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function carrier_ranges()
    {
        return $this->belongsTo(CarrierRange::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
