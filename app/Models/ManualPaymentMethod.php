<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class ManualPaymentMethod extends Model
{
    use PreventDemoModeChanges;

    protected $guarded = [];
}
