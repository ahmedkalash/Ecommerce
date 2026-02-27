<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class FlashDealTranslation extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['title', 'lang', 'flash_deal_id'];

    public function flash_deal()
    {
        return $this->belongsTo(FlashDeal::class);
    }
}
