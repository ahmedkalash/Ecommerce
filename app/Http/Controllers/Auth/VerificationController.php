<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\HasNotVerifiedEmail;
use App\Models\User;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->middleware(HasNotVerifiedEmail::class)->only('show', 'resend', 'verify');
    }

    public function redirectTo()
    {
        // 1. 'pull' fetches the intended URL AND deletes it from the session immediately.
        // 2. The second argument is the fallback (if no intended URL exists).
        return session()->pull('url.intended', \request()->user()->homePage());
    }

    /**
     * Show the email verification notice.
     **/
    public function show(Request $request)
    {
        return view('auth.'.get_setting('authentication_layout_select').'.verify_email');
    }

    protected function verified(Request $request): void
    {
        flash(translate('Your email has been verified successfully'))->success();
    }

    public function emailChangeCallback(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param = $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();

            if ($user != null) {

                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();

                auth()->login($user, true);

                flash(translate('Email Changed successfully'))->success();
                if ($user->user_type == 'seller') {
                    return redirect()->route('seller.dashboard');
                }

                return redirect()->route('dashboard');
            }
        }

        flash(translate('Email was not verified. Please resend your mail!'))->error();

        return redirect()->route('dashboard');
    }
}
