<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class CustomerPackagePayment extends Model
{
    use PreventDemoModeChanges;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer_package()
    {
        return $this->belongsTo(CustomerPackage::class);
    }
}
