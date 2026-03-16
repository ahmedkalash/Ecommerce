<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use App\Settings\FeatureToggleSettings;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class UserRegistrationService
{
    /**
     * Create a new user instance after a valid registration.
     *
     * @throws ValidationException
     */
    public function create(UserDTO $userDTO): User
    {
        $user = User::create([
            'name' => $userDTO->name,
            'email' => strtolower($userDTO->email), // Store email in lowercase for case-insensitive login
            'user_type' => $userDTO->user_type->value,
            'password' => Hash::make($userDTO->password),
        ]);

        return $user->refresh();
    }

    /**
     * @throws \Throwable
     */
    public function handlePostRegistration(User $user): User
    {
        if ($this->shouldAutoVerify()) {
            $this->autoVerifyUser($user);
            toast(__('customer/register_page.registration_successful'), 'success');
        } elseif ($this->sendVerificationEmail($user)) {
            toast(__('customer/register_page.verify_email_sent'), 'success');
        } else {
            // Show message but allow user to request resend
            toast(__('customer/register_page.verify_email_failed'), 'warning');
        }
        $this->notifyAdmin($user);

        return $user;
    }

    protected function shouldAutoVerify(): bool
    {
        return app(FeatureToggleSettings::class)->email_verification != 1;
    }

    protected function autoVerifyUser(User $user): void
    {
        $user->markEmailAsVerified();
    }

    protected function sendVerificationEmail(User $user): bool
    {
        try {
            $user->sendEmailVerificationNotification();
            $sent = true;
            \Log::info('Sending Email Verification Notification to '.$user->email.' ...');
        } catch (\Throwable $e) {
            $sent = false;
            \Log::error('Sending Email Verification Notification Failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $sent;
    }

    protected function notifyAdmin(User $user): void {}

    /**
     * Get the guard to be used during registration.
     */
    public function guard(): Guard|StatefulGuard
    {
        return Auth::guard('web');
    }

    /**
     * @return Response
     */
    public function registrationResponse(User $user)
    {
        if ($this->shouldAutoVerify()) {
            return redirect()->intended($user->homePage());
        }

        return redirect()->route('verification.notice');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:190'],
            'email' => ['required', 'email', 'unique:users,email', 'string', 'max:190'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'agree_to_terms' => ['required', 'accepted', 'max:2'],
        ];
    }
}
