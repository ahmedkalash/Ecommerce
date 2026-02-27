<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class WarrantyTranslation extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['text', 'lang', 'warranty_id'];

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }
}
