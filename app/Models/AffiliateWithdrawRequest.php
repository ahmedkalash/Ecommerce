<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class AffiliateWithdrawRequest extends Model
{
    use PreventDemoModeChanges;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
