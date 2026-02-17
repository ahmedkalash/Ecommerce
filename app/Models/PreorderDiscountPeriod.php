<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderDiscountPeriod extends Model
{
    use HasFactory,PreventDemoModeChanges;
}
