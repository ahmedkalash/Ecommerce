<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the user ID associated with the staff member being updated
        // route parameter is 'staff' which is the staff ID
        // we need to find the user_id from staff table or assume staff ID is passed

        $staffId = $this->route('staff');
        $staff = \App\Models\Staff::findOrFail($staffId);
        $userId = $staff->user_id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$userId,
            'mobile' => 'required|string|max:20',
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ];
    }
}
