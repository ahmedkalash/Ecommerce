<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
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
            'id' => ['required', 'integer', Rule::exists('users', 'id')],
            'name' => ['required', 'string', 'max:190'],
            'email' => [
                'required',
                'string',
                'email',
                'max:190',
                Rule::unique('users', 'email')->ignore($this->route('staff')),
            ],
            'mobile' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],
            'role_id' => ['required', 'exists:roles,id'],
        ];
    }

    /**
     * Get data to be validated from the request.
     */
    public function validationData(): array
    {
        return array_merge($this->all(), [
            'id' => $this->route('staff'),
        ]);
    }
}
