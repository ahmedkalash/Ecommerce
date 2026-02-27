<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['email', 'token'];
}
