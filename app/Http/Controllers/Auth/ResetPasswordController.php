<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords {
        reset as baseReset;
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
     * Reset the given user's password.
     *
     * @return RedirectResponse|JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        if (UserService::isBanned($request->email)) {
            flash(translate('Your account has been banned.'))->error();

            return back()->withInput($request->only('email'));
        }

        return $this->baseReset($request);
    }

    /**
     * Display the password reset view for the given token.
     *
     * @return Factory|View
     */
    public function showResetForm(Request $request, ?string $token = null)
    {
        return view('auth.'.get_setting('authentication_layout_select').'.reset_password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Get the Post-Reset Redirect Path
     */
    public function redirectPath()
    {
        return auth()->user()->homePage();
    }
}
