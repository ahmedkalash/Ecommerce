<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OTPVerificationController;
use App\Models\BusinessSetting;
use App\Models\Cart;
use App\Models\User;
use App\Rules\Recaptcha;
use App\Utility\EmailUtility;
use Cookie;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Session;

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

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        /**
         * Toggles a "Verification First" registration flow for new customers.
         *
         * @see /.docs/business_settings/customer_registration_verify.md
         */
        if (get_setting('customer_registration_verify') === '1') {
            abort(404);
        }

        if (Auth::check()) {
            return redirect()->route('home');
        }

        // // todo: install and handel affiliate_system addon
        //        if ($request->has('referral_code') && addon_is_activated('affiliate_system')) {
        //            try {
        //                $affiliate_validation_time = AffiliateConfig::where('type', 'validation_time')->first();
        //                $cookie_minute = 30 * 24;
        //                if ($affiliate_validation_time) {
        //                    $cookie_minute = $affiliate_validation_time->value * 60;
        //                }
        //
        //                Cookie::queue('referral_code', $request->referral_code, $cookie_minute);
        //                $referred_by_user = User::where('referral_code', $request->referral_code)->first();
        //
        //                $affiliateController = new AffiliateController;
        //                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
        //            } catch (\Exception $e) {
        //            }
        //        }
        $email = null;
        $phone = null;

        return view('auth.'.get_setting('authentication_layout_select').'.user_registration',
            compact('email', 'phone'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'g-recaptcha-response' => [
                Rule::when(
                    get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_register') == 1,
                    ['required', new Recaptcha],
                    ['sometimes']
                ),
            ],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     *
     * @throws ValidationException
     */
    protected function create(array $data)
    {
        if (isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        } else {
            if (addon_is_activated('otp_system')) {
                $cleanPhone = preg_replace('/\D+/', '', $data['phone']);
                $user = User::create([
                    'name' => $data['name'],
                    'phone' => '+'.$data['country_code'].$cleanPhone,
                    'password' => Hash::make($data['password']),
                    'verification_code' => rand(100000, 999999),
                ]);

                if (get_setting('customer_registration_verify') != '1') {
                    $otpController = new OTPVerificationController;
                    $otpController->send_code($user);
                }
            }
        }
        // Todo: fix this mess
        if (!isset($user)) {
            throw new ValidationException('User creation failed');
        }

        return $user->refresh();
    }

    public function register(Request $request)
    {
        // dd($request->all());
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            if (User::where('email', $request->email)->first() != null) {
                flash(translate('Email or Phone already exists.'));
                if (get_setting('customer_registration_verify') == 1) {
                    return route('registration.verification');
                }

                return back();
            }
        } elseif (User::where('phone', '+'.$request->country_code.$request->phone)->first() != null) {
            flash(translate('Phone already exists.'));
            if (get_setting('customer_registration_verify') == 1) {
                return route('registration.verification');
            }

            return back();
        }

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        $this->guard()->login($user);
        $this->handelCart();
        $this->handelReferralCode($user);

        if ($user->email != null) {
            if (BusinessSetting::where(
                    'type',
                    'email_verification'
                )->first()->value != 1 || get_setting('customer_registration_verify') === '1') {
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->save();
                offerUserWelcomeCoupon();
                flash(translate('Registration successful.'))->success();
            } else {
                try {
                    EmailUtility::email_verification($user, 'customer');
                    flash(translate('Registration successful. Please verify your email.'))->success();
                } catch (\Throwable $e) {
                    // dd($e);
                    $user->delete();
                    flash(translate('Registration failed. Please try again later.'))->error();
                }
            }

            // Account Opening Email to customer
            if ($user != null && (get_email_template_data('registration_email_to_customer', 'status') == 1)) {
                try {
                    EmailUtility::customer_registration_email('registration_email_to_customer', $user, null);
                } catch (\Exception $e) {
                }
            }
        }

        if ($user->phone != null) {
            if (get_setting('email_verification') != 1 || get_setting('customer_registration_verify') === '1') {
                $user->email_verified_at = date('Y-m-d H:m:s');
                $user->save();
                offerUserWelcomeCoupon();
                flash(translate('Registration successful.'))->success();
            }
        }

        // customer Account Opening Email to Admin
        if ($user != null && (get_email_template_data('customer_reg_email_to_admin', 'status') == 1)) {
            try {
                EmailUtility::customer_registration_email('customer_reg_email_to_admin', $user, null);
            } catch (\Exception $e) {
            }
        }

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user)
    {
        if ($user->email == null && $user->email_verified_at == null) {
            return redirect()->route('verification');
        } elseif (session('link') != null) {
            return redirect(session('link'));
        } else {
            return redirect()->route('home');
        }
    }

    private function handelCart()
    {
        if (session('temp_user_id') != null) {
            if (auth()->user()?->user_type == 'customer') {

                Cart::where('temp_user_id', session('temp_user_id'))
                    ->update(
                        [
                            'user_id' => auth()->user()->id,
                            'temp_user_id' => null,
                        ]
                    );
            } else {
                Cart::where('temp_user_id', session('temp_user_id'))->delete();
            }
            Session::forget('temp_user_id');
        }
    }

    private function handelReferralCode(User $user)
    {
        if ($referral_code = Cookie::get('referral_code')) {
            $referred_by_user = User::where('referral_code', $referral_code)->first();
            if ($referred_by_user != null) {
                $user->referred_by = $referred_by_user->id;
                $user->save();
            }
        }
    }
}
