<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

/**@see app/Http/Controllers/Auth/LoginController.php*/
class Login extends Component
{
    use AuthenticatesUsers;

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                function ($attribute, $value, $fail) {
                    $user = User::where('email', $value)->first();
                    if ($user && $user->isBanned()) {
                        $fail(__('auth.banned'));
                    }
                },
            ],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->intended(auth()->user()->homePage());
        }

        $this->validate();

        $request = request()->merge([
            'email' => $this->email,
            'password' => $this->password,
            'remember' => $this->remember,
        ]);

        return $this->login($request);
    }

    protected function validateLogin(Request $request): void
    {
        // Logic moved to $this->validate(), and we keep it empty to override parent trait method validation logic,
        // to prevent any conflicts
    }

    protected function sendLoginResponse(Request $request)
    {
        // Livewire synthesizes test requests without a session store bound to the $request object.
        // Bypassing $request->session()->regenerate() with the global helper completely prevents test crashes!
        session()->regenerate();

        $this->clearLoginAttempts($request);

        app(CartService::class)->handelCartAfterAuthentication();

        if (! auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended(auth()->user()->homePage());
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
