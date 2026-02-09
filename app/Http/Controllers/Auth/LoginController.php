<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\CartService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        login as baseLogin;
    }

    public function __construct(
        protected CartService $cartService
    ) {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.' . get_setting('authentication_layout_select') . '.user_login');
    }

    public function showSellerLoginForm()
    {
        if (get_setting('vendor_system_activation') == 1) {
            return view('auth.' . get_setting('authentication_layout_select') . '.seller_login');
        }

        return redirect()->route('home');
    }

    public function showDeliveryBoyLoginForm()
    {
        if (addon_is_activated('delivery_boy')) {
            return view('auth.' . get_setting('authentication_layout_select') . '.deliveryboy_login');
        }

        return redirect()->route('home');
    }

    public function login(LoginRequest $request)
    {
        return $this->baseLogin($request);
    }

    /**
     * @throws ValidationException
     */
    protected function validateLogin(Request $request): void
    {
        // Logic moved to LoginRequest, and we keep it empty to override parent trait method validation logic,
        // so it does not make a conflict
    }

    /**
     * Get the login credentials from the request.
     * Handles both email and phone authentication.
     */
    protected function credentials(Request $request): array
    {
        // Phone login: combine country_code + phone
        if ($request->filled('phone') && $request->filled('country_code')) {
            return [
                'phone' => '+' . $request->input('country_code') . $request->input('phone'),
                'password' => $request->input('password'),
            ];
        }

        // Email login (default)
        return $request->only($this->username(), 'password');
    }

    /**
     * Check a user's role and redirect user based on their role.
     * Called after successful authentication.
     */
    public function authenticated()
    {
        $this->cartService->handelCartAfterAuthentication();

        if (auth()->user()->isSeller()) {
            // Block unapproved sellers
            if (!auth()->user()->isShopApproved()) {
                auth()->logout();
                flash(translate('Your seller account is under review. We will notify you once approved.'));

                return redirect()->route('home');
            }

            // Log seller login
            Log::channel('seller_login')->info('Seller Logged In', [
                'user_id' => auth()->user()->id,
                'email' => auth()->user()->email,
                'time' => now()->toDateTimeString(),
            ]);
        }

        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (session()->has('link')) {
            return redirect(session()->pull('link'));
        }

        return redirect()->intended(auth()->user()->homePage());
    }

    public function handle_demo_login()
    {
        return view('frontend.handle_demo_login');
    }
}
