<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class CommissionHistory extends Model
{
    use PreventDemoModeChanges;

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
