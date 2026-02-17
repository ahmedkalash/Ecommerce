<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class CustomerProductTranslation extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['customer_product_id', 'name', 'lang'];

    public function customer_product()
    {
        return $this->belongsTo(CustomerProduct::class);
    }
}
