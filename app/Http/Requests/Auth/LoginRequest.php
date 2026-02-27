<?php

namespace App\Http\Requests\Auth;

use App\Enums\RecaptchaAction;
use App\Services\RecaptchaService;
use Illuminate\Foundation\Http\FormRequest;

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
            'email' => ['required_without:phone', 'nullable', 'email', 'string', 'max:190'],
            'phone' => ['required_without:email', 'nullable', 'string', 'min:6', 'max:20'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'], // Checkbox sends "on", not boolean
            'g-recaptcha-response' => RecaptchaService::validationRules(RecaptchaAction::CUSTOMER_LOGIN),
        ];
    }
}
