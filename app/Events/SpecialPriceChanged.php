<?php

namespace App\Events;

use App\Models\ProductStock;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a ProductStock's special price columns are modified.
 *
 * Consumers can listen for this to invalidate caches, send notifications, etc.
 */
class SpecialPriceChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ProductStock $variant,
    ) {}
}
