<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RecaptchaAction;
use App\Http\Controllers\Controller;
use App\Services\RecaptchaService;
use App\Services\UserService;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails {
        sendResetLinkEmail as baseSendResetLinkEmail;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @return RedirectResponse|JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(RecaptchaService::validationRules(RecaptchaAction::FORGOT_PASSWORD));

        // Check if a user is banned
        if (UserService::isBanned($request->email)) {
            return back()->withErrors(['email' => translate('Your account has been banned.')]);
        }

        return $this->baseSendResetLinkEmail($request);
    }
}
