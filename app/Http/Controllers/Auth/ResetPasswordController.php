<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    use ResetsPasswords;

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
     * Get the response for a successful password reset.
     *
     * @param  string  $response
     */
    protected function sendResetResponse(Request $request, $response)
    {
        if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
            return redirect()->route('admin.dashboard')
                ->with('status', trans($response));
        }

        return redirect()->route('home')
            ->with('status', trans($response));
    }

    public function resetWithCode(Request $request)
    {
        if (($user = User::where('email', $request->email)->where(
                'verification_code',
                $request->code
            )->first()) != null) {
            if ($request->password == $request->password_confirmation) {
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                flash(translate('Password updated successfully'))->success();

                if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('home');
            } else {
                flash(translate("Password and confirm password didn't match"))->warning();

                return view('auth.'.get_setting('authentication_layout_select').'.reset_password');
            }
        } else {
            flash(translate('Verification code mismatch'))->error();

            return view('auth.'.get_setting('authentication_layout_select').'.reset_password');
        }
    }
}
