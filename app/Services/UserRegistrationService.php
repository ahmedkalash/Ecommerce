<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\Cart;
use App\Models\User;
use App\Utility\EmailUtility;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class UserRegistrationService
{
    use RedirectsUsers;

    public function __construct(
        protected string $redirectTo = '/'
    ) {
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @throws ValidationException
     */
    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_type' => UserType::CUSTOMER->value,
            'password' => Hash::make($data['password']),
        ]);

        if (addon_is_activated('otp_system')) {
            // Todo: add registration with phone number
        }

        return $user->refresh();
    }

    public function getRegistrationView(): string
    {
        return 'auth.'.get_setting('authentication_layout_select').'.user_registration';
    }

    /**
     * @throws \Throwable
     */
    public function handlePostRegistration(User $user): void
    {
        if ($this->shouldAutoVerify()) {
            $this->autoVerifyUser($user);
        } else {
            $this->sendVerificationEmail($user);
            flash(translate('Registration successful. Please verify your email.'))->success();
        }

        $this->sendWelcomeEmail($user);
        $this->notifyAdmin($user);

    }

    protected function shouldAutoVerify(): bool
    {
        $emailVerificationDisabled = get_setting('email_verification') != 1;
        $alreadyVerifiedBeforeRegistration = get_setting('customer_registration_verify') === '1';

        return $emailVerificationDisabled || $alreadyVerifiedBeforeRegistration;
    }

    protected function autoVerifyUser(User $user): void
    {
        $user->markEmailAsVerified();
        offerUserWelcomeCoupon();  // Todo: check this
        flash(translate('Registration successful.'))->success();
    }

    protected function sendVerificationEmail(User $user): void
    {
        try {
            // Todo: check this
            EmailUtility::email_verification($user, UserType::CUSTOMER->value);

        } catch (\Throwable $e) {

            \Log::error('Email verification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            // Show message but allow user to request resend
            flash(translate('Registration successful. Verification email could not be sent. Please request a new one.'))->warning();
        }
    }

    protected function sendWelcomeEmail(User $user): void
    {
        // Todo: check this
        if (!get_email_template_data('registration_email_to_customer', 'status')) {
            return;
        }

        try {
            // Todo: check this
            EmailUtility::customer_registration_email('registration_email_to_customer', $user);
        } catch (\Exception $e) {
            \Log::warning('Welcome email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }
    }

    protected function notifyAdmin(User $user): void
    {
        // Todo: check this
        if (!get_email_template_data('customer_reg_email_to_admin', 'status')) {
            return;
        }

        try {
            // Todo: check this
            EmailUtility::customer_registration_email('customer_reg_email_to_admin', $user);
        } catch (\Exception $e) {
            \Log::warning('Admin notification failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get the guard to be used during registration.
     */
    public function guard(): Guard|StatefulGuard
    {
        return Auth::guard();
    }

    public function registrationResponse(Request $request, User $user): RedirectResponse
    {
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification');
        } elseif (session('link') != null) {
            return redirect()->to(session('link'));
        } else {
            return redirect()->route('home');
        }
    }

    public function handelCart(): void
    {
        // Todo: check this
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

    public function handelReferralCode(User $user): void
    {
        // Todo: check this
        if ($referral_code = Cookie::get('referral_code')) {
            $referred_by_user = User::where('referral_code', $referral_code)->first();
            if ($referred_by_user != null) {
                $user->referred_by = $referred_by_user->id;
                $user->save();
            }
        }
    }

    public function handelAffiliateSystem(Request $request): void
    {
        // Todo: check this
        if ($request->has('referral_code') && addon_is_activated('affiliate_system')) {
            try {
                $affiliate_validation_time = AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }

                Cookie::queue('referral_code', $request->referral_code, $cookie_minute);
                $referred_by_user = User::where('referral_code', $request->referral_code)->first();

                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            } catch (\Exception $e) {
            }
        }
    }
}
