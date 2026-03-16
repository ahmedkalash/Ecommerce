<?php

namespace App\Livewire\Auth;

use App\DTOs\UserDTO;
use App\Enums\UserType;
use App\Services\CartService;
use App\Services\UserRegistrationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**@see app/Http/Controllers/Auth/RegisterController.php*/
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $agree_to_terms = false;

    public function rules(): array
    {
        return app(UserRegistrationService::class)->rules();
    }

    /**
     * Triggered on every update of $email (wire:model.live).
     * Validates only the email field to give real-time feedback.
     */
    public function updatedEmail(): void
    {
        $this->validateOnly('email');
    }

    /** Real-time validation for name field. */
    public function updatedName(): void
    {
        $this->validateOnly('name');
    }

    /**
     * Real-time validation for password.
     * Validates both password and password_confirmation so the
     * "confirmed" rule (which compares both fields) always stays in sync.
     */
    public function updatedPassword(): void
    {
        $this->validateOnly('password');
    }

    /** Re-validate password when confirmation field changes. */
    public function updatedPasswordConfirmation(): void
    {
        $this->validateOnly('password');
    }

    /** Real-time validation for the terms checkbox. */
    public function updatedAgreeToTerms(): void
    {
        $this->validateOnly('agree_to_terms');
    }

    /**
     * @throws \Throwable
     */
    public function register(UserRegistrationService $registrationService, CartService $cartService)
    {
        if (Auth::check()) {
            return redirect()->intended(auth()->user()->homePage());
        }

        $this->validate();

        $user = DB::transaction(function () use ($registrationService, $cartService, &$user) {
            $user = $registrationService->create(UserDTO::fromArray([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'user_type' => UserType::CUSTOMER,
            ]));

            $user = $registrationService->handlePostRegistration($user);
            $registrationService->guard()->login($user);
            $cartService->handelCartAfterAuthentication();
            event(new Registered($user));

            return $user;
        });

        return $registrationService->registrationResponse($user) ?:
            redirect()->intended($user->homePage());
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
