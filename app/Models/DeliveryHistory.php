<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class DeliveryHistory extends Model
{
    use PreventDemoModeChanges;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
