<?php

namespace App\Http\Requests\Auth;

use App\Enums\RecaptchaAction;
use App\Rules\Recaptcha;
use App\Services\RecaptchaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'string', 'max:190'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
            'g-recaptcha-response' => RecaptchaService::validationRules(RecaptchaAction::CUSTOMER_LOGIN),
        ];
    }
}
