<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class ElementStyle extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = [
        'value',
    ];

    public function elementType()
    {
        return $this->belongsTo(ElementType::class);
    }
}
