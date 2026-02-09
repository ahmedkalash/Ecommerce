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
                $guestCarts = Cart::where('temp_user_id', session('temp_user_id'))->get();

                foreach ($guestCarts as $guestCart) {
                    // Check if user already has this product in their cart
                    $existingCart = Cart::where('user_id', auth()->user()->id)
                        ->where('product_id', $guestCart->product_id)
                        ->first();

                    if ($existingCart) {
                        // Merge quantities if product exists
                        $existingCart->quantity += $guestCart->quantity;
                        $existingCart->save();
                        $guestCart->delete();
                    } else {
                        // Transfer cart item to user
                        $guestCart->update([
                            'user_id' => auth()->user()->id,
                            'temp_user_id' => null,
                        ]);
                    }
                }
            } else {
                Cart::where('temp_user_id', session('temp_user_id'))->delete();
            }
            Session::forget('temp_user_id');
        }
    }
}
