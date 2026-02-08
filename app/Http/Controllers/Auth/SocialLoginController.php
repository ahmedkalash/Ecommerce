<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use App\Utility\EmailUtility;
use Illuminate\Http\Request;
use Session;
use Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToProvider($provider)
    {
        if (request()->get('query') == 'mobile_app') {
            request()->session()->put('login_from', 'mobile_app');
        }
        if ($provider == 'apple') {
            return Socialite::driver('sign-in-with-apple')
                ->scopes(['name', 'email'])
                ->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleAppleCallback(Request $request)
    {
        try {
            $user = Socialite::driver('sign-in-with-apple')->user();
        } catch (\Exception $e) {
            flash(translate('Something Went wrong. Please try again.'))->error();

            return redirect()->route('user.login');
        }
        // check if provider_id exist
        $existingUserByProviderId = User::where('provider_id', $user->id)->first();

        if ($existingUserByProviderId) {
            $existingUserByProviderId->access_token = $user->token;
            $existingUserByProviderId->refresh_token = $user->refreshToken;
            if (!isset($user->user['is_private_email'])) {
                $existingUserByProviderId->email = $user->email;
            }
            $existingUserByProviderId->save();
            // proceed to login
            auth()->login($existingUserByProviderId, true);
        } else {
            // check if email exist
            $existing_or_new_user = User::firstOrNew([
                'email' => $user->email,
            ]);
            $existing_or_new_user->provider_id = $user->id;
            $existing_or_new_user->access_token = $user->token;
            $existing_or_new_user->refresh_token = $user->refreshToken;
            $existing_or_new_user->provider = 'apple';
            if (!$existing_or_new_user->exists) {
                $existing_or_new_user->name = 'Apple User';
                if ($user->name) {
                    $existing_or_new_user->name = $user->name;
                }
                $existing_or_new_user->email = $user->email;
                $existing_or_new_user->email_verified_at = date('Y-m-d H:m:s');
            }
            $existing_or_new_user->save();

            auth()->login($existing_or_new_user, true);
        }

        if (session('temp_user_id') != null) {
            Cart::where(
                'user_id',
                auth()->user()->id
            )->delete(); // If previous data is available for this user, delete first
            Cart::where('temp_user_id', session('temp_user_id'))
                ->update([
                    'user_id' => auth()->user()->id,
                    'temp_user_id' => null,
                ]);

            Session::forget('temp_user_id');
        }

        return redirect()->intended(auth()->user()->homePage());
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        if (session('login_from') == 'mobile_app') {
            return $this->mobileHandleProviderCallback($request, $provider);
        }
        try {
            if ($provider == 'twitter') {
                $user = Socialite::driver('twitter')->user();
            } else {
                $user = Socialite::driver($provider)->stateless()->user();
            }
        } catch (\Exception $e) {
            flash(translate('Something Went wrong. Please try again.'))->error();

            return redirect()->route('user.login');
        }

        // check if provider_id exist
        $existingUserByProviderId = User::where('provider_id', $user->id)->first();

        if ($existingUserByProviderId) {
            $existingUserByProviderId->access_token = $user->token;
            $existingUserByProviderId->save();
            // proceed to login
            auth()->login($existingUserByProviderId, true);
        } else {
            // check if email exist
            $existingUser = User::where('email', '!=', null)->where('email', $user->email)->first();

            if ($existingUser) {
                // update provider_id
                $existing_User = $existingUser;
                $existing_User->provider_id = $user->id;
                $existing_User->provider = $provider;
                $existing_User->access_token = $user->token;
                $existing_User->save();

                // proceed to login
                auth()->login($existing_User, true);
            } else {
                // create a new user
                $newUser = new User;
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->email_verified_at = date('Y-m-d H:i:s');
                $newUser->provider_id = $user->id;
                $newUser->provider = $provider;
                $newUser->access_token = $user->token;
                $newUser->save();
                // proceed to login
                auth()->login($newUser, true);

                // customer Account Opening Email to Admin
                if ((get_email_template_data('customer_reg_email_to_admin', 'status') == 1)) {
                    try {
                        EmailUtility::customer_registration_email('customer_reg_email_to_admin', $newUser, null);
                    } catch (\Exception $e) {
                    }
                }
            }
        }

        if (session('temp_user_id') != null) {
            // Deleting cart data if the user has already cart data.
            Cart::where('user_id', auth()->user()->id)->delete();

            Cart::where('temp_user_id', session('temp_user_id'))
                ->update([
                    'user_id' => auth()->user()->id,
                    'temp_user_id' => null,
                ]);

            Session::forget('temp_user_id');
        }

        return redirect()->intended(auth()->user()->homePage());
    }

    public function mobileHandleProviderCallback($request, $provider)
    {
        $return_provider = '';
        $result = false;
        if ($provider) {
            $return_provider = $provider;
            $result = true;
        }

        return response()->json([
            'result' => $result,
            'provider' => $return_provider,
        ]);
    }
}
