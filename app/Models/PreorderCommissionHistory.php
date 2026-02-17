<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class PreorderCommissionHistory extends Model
{
    use PreventDemoModeChanges,PreventDemoModeChanges;

    public function preorder()
    {
        return $this->belongsTo(Preorder::class);
    }
}
