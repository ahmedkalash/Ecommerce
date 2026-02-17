<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['page_id', 'title', 'content', 'lang'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
