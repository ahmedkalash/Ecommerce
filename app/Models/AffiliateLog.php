<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class AffiliateLog extends Model
{
    use PreventDemoModeChanges;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_detail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
