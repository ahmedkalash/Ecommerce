<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckRegistrationFirstFlow;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\UserRegistrationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Where to redirect users after registration.
     */
    protected string $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected UserRegistrationService $registrationService
    ) {
        $this->middleware('guest');
        $this->middleware('handle-demo-login');
        $this->middleware(CheckRegistrationFirstFlow::class)->only([
            'showRegistrationForm',
            'register',
        ]);
    }

    public function showRegistrationForm(Request $request)
    {
        // todo: install and handel affiliate_system addon
        $this->registrationService->handelAffiliateSystem($request);
        $email = null;
        $phone = null;
        $view = $this->registrationService->getRegistrationView();

        return view($view, compact('email', 'phone'));
    }

    /**
     * @throws \Throwable
     */
    public function register(RegisterRequest $request)
    {
        $user = null;
        DB::transaction(function () use ($request, &$user) {
            $user = $this->registrationService->create($request->all());
            $this->registrationService->guard()->login($user);
            $this->registrationService->handelCart();
            $this->registrationService->handelReferralCode($user);
            $this->registrationService->handlePostRegistration($user);
            event(new Registered($user));
        });

        return $this->registrationService->registrationResponse($request, $user) ?:
            redirect($this->registrationService->redirectPath());
    }
}
