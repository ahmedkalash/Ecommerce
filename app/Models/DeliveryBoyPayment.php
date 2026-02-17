<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryBoyPayment extends Model
{
    use HasFactory, PreventDemoModeChanges;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
