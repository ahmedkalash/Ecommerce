<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class AffiliateUser extends Model
{
    use PreventDemoModeChanges;

    //
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function affiliate_payments()
    {
        return $this->hasMany(AffiliatePayment::class)->orderBy('created_at', 'desc');
    }
}
