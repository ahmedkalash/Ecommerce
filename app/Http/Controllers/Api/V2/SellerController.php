<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\ShopCollection;

class SellerController extends Controller
{
    public function topSellers()
    {
        $best_selers = get_best_sellers(5);

        return new ShopCollection($best_selers);
    }
}
