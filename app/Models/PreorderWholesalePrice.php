<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderWholesalePrice extends Model
{
    use HasFactory,PreventDemoModeChanges;
}
