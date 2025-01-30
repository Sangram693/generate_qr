<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check(); // Allow only authenticated users
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = Auth::user();
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users',
            'user_name' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|same:password',
            'role' => 'required|in:admin,user',
        ];

        // If Super Admin is creating a User, admin_id is required
        if ($this->role === 'user' && $user->role === 'super_admin') {
            $rules['admin_id'] = 'required|exists:users,id';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'phone.required' => 'Phone is required',
            'password.required' => 'Password is required',
            'password_confirmation.required' => 'Password confirmation is required',
            'email.unique' => 'Email is already taken',
            'phone.unique' => 'Phone is already taken',
            'user_name.required' => 'User Name is required',
            'user_name.unique' => 'User Name is already taken',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'password_confirmation.same' => 'Password confirmation does not match',
            'role.required' => 'Role is required',
            'role.in' => 'Role should be either admin or user',
            'admin_id.required' => 'Admin ID is required when Super Admin creates a user',
            'admin_id.exists' => 'The provided Admin ID does not exist in the users table',
        ];
    }
}
