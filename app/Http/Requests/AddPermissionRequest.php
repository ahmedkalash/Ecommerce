<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPermissionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:190', 'unique:permissions,name'],
            'parent' => ['required', 'string', 'max:190'],
        ];
    }
}
