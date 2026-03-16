<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LocaleService;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**@see app/Livewire/Auth/Login.php*/
class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    public function showSellerLoginForm()
    {
        return 'SellerLoginForm';
    }

    public function showDeliveryBoyLoginForm()
    {
        return 'DeliveryBoyLoginForm';

    }

    public function logout(Request $request)
    {
        $current_locale = LocaleService::getCurrentFullLocale();

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        app(LocaleService::class)->setCurrentLocale($current_locale);

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : to_route('home');
    }

    /**
     * The user has logged out of the application.
     *
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('web');
    }
}
