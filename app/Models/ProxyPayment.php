<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class ProxyPayment extends Model
{
    use PreventDemoModeChanges;

    protected $table = 'proxypay_payments';
}
