<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the user profile page.
     */
    public function show()
    {
        if (Auth::user()->isSeller()) {
            return redirect()->route('seller.profile.index');
        } elseif (Auth::user()->isDeliveryBoy()) {
            return view('delivery_boys.profile');
        } else {
            return view('frontend.user.profile');
        }
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        if (config('app.env') == 'DEMO') {
            flash(translate('Sorry! the action is not permitted in demo '))->error();

            return back();
        }

        $user = $request->user();
        $data = $request->validated();

        if (isset($data['photo'])) {
            $user->avatar_original = $data['photo'];
            unset($data['photo']);
        }

        $user->fill($data);
        $user->save();

        flash(translate('Your Profile has been updated successfully!'))->success();

        return back();
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        if (config('app.env') == 'DEMO') {
            flash(translate('Sorry! the action is not permitted in demo '))->error();

            return back();
        }

        $user = $request->user();

        $user->password = Hash::make($request->new_password);
        $user->save();

        flash(translate('Password updated successfully!'))->success();

        return back();
    }

    public function verifyEmailCode(\Illuminate\Http\Request $request)
    {
        return response()->json(json_encode([
            'status' => 1,
            'message' => 'Verification code sent successfully',
        ]));
    }
}
