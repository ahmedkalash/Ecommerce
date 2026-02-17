<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use PreventDemoModeChanges;

    protected $guarded = [];
}
