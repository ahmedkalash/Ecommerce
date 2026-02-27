<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory,PreventDemoModeChanges;

    protected $fillable = ['name', 'status'];

    public function carrier_range_prices()
    {
        return $this->hasMany(CarrierRangePrice::class);
    }
}
