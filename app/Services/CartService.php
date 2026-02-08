<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function handelCartAfterAuthentication(): void
    {
        // Todo: check this with the cart task
        if (session('temp_user_id') != null) {
            if (auth()->user()?->isCustomer()) {
                Cart::where('temp_user_id', session('temp_user_id'))
                    ->update([
                        'user_id' => auth()->user()->id,
                        'temp_user_id' => null,
                    ]);
            } else {
                Cart::where('temp_user_id', session('temp_user_id'))->delete();
            }
            Session::forget('temp_user_id');
        }
    }
}
