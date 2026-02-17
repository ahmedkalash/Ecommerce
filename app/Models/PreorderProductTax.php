<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderProductTax extends Model
{
    use HasFactory,PreventDemoModeChanges;

    public function preorder_tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }
}
