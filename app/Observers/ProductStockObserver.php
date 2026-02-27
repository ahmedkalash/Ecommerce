<?php

namespace App\Observers;

use App\Events\SpecialPriceChanged;
use App\Models\ProductStock;

/**
 * Observes ProductStock model lifecycle events.
 *
 * Fires SpecialPriceChanged when any special_price_* column is dirty.
 */
class ProductStockObserver
{
    /**
     * Handle the ProductStock "updating" event.
     */
    public function updating(ProductStock $stock): void
    {
        if ($stock->isDirty(['special_price', 'special_price_type', 'special_price_start', 'special_price_end'])) {
            event(new SpecialPriceChanged($stock));
        }
    }
}
