<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\Wishlist;
use Hash;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function counters()
    {
        return response()->json([
            'cart_item_count' => Cart::where('user_id', auth()->user()->id)->count(),
            'wishlist_item_count' => Wishlist::where('user_id', auth()->user()->id)->count(),
            'order_count' => Order::where('user_id', auth()->user()->id)->count(),
        ]);
    }

    public function preorderProducts()
    {
        // Assuming the user intended to add a new function or modify the existing one.
        // The provided snippet was syntactically incorrect.
        // This is a placeholder for the intended logic of preorderProducts.
        // If it was meant to replace 'counters', the name should be changed and the body adjusted.
        // For now, I'm adding an empty function to fix the syntax error from the instruction.
        return response()->json([
            'result' => false,
            'message' => 'Preorder products logic not implemented yet.',
        ]);
    }

    public function update(Request $request)
    {
        $user = User::find(auth()->user()->id);
        if (! $user) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found.'),
            ]);
        }

        if (isset($request->name)) {
            $user->name = $request->name;
        }
        if (isset($request->phone)) {
            $user->phone = $request->phone;
        }

        if (isset($request->password)) {
            if ($request->password != '') {
                $user->password = Hash::make($request->password);
            }
        }
        $user->save();

        return response()->json([
            'result' => true,
            'message' => translate('Profile information updated'),
        ]);
    }

    public function update_device_token(Request $request)
    {
        $user = User::find(auth()->user()->id);
        if (! $user) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found.'),
            ]);
        }

        $user->device_token = $request->device_token;

        $user->save();

        return response()->json([
            'result' => true,
            'message' => translate('device token updated'),
        ]);
    }

    public function updateImage(Request $request)
    {
        $user = User::find(auth()->user()->id);
        if (! $user) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found.'),
                'path' => '',
            ]);
        }

        try {
            $image = $request->image;
            $filename = $request->filename;

            $media = $user->addMediaFromBase64($image)
                ->usingFileName($filename)
                ->toMediaCollection('uploads');

            $user->avatar_original = $media->id;
            $user->save();

            return response()->json([
                'result' => true,
                'message' => translate('Image updated'),
                'path' => get_file_by_id($media->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
                'path' => '',
            ]);
        }
    }

    // not user profile image but any other base 64 image through uploader
    public function imageUpload(Request $request)
    {
        $user = User::find(auth()->user()->id);
        if (! $user) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found.'),
                'path' => '',
                'upload_id' => 0,
            ]);
        }

        try {
            $image = $request->image;
            $filename = $request->filename;

            $media = $user->addMediaFromBase64($image)
                ->usingFileName($filename)
                ->toMediaCollection('uploads');

            return response()->json([
                'result' => true,
                'message' => translate('Image updated'),
                'path' => get_file_by_id($media->id),
                'upload_id' => $media->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
                'path' => '',
                'upload_id' => 0,
            ]);
        }
    }

    public function checkIfPhoneAndEmailAvailable()
    {
        $phone_available = false;
        $email_available = false;
        $phone_available_message = translate('User phone number not found');
        $email_available_message = translate('User email  not found');

        $user = User::find(auth()->user()->id);

        if ($user->phone != null || $user->phone != '') {
            $phone_available = true;
            $phone_available_message = translate('User phone number found');
        }

        if ($user->email != null || $user->email != '') {
            $email_available = true;
            $email_available_message = translate('User email found');
        }

        return response()->json(
            [
                'phone_available' => $phone_available,
                'email_available' => $email_available,
                'phone_available_message' => $phone_available_message,
                'email_available_message' => $email_available_message,
            ]
        );
    }
}
