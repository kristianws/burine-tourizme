<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'fullname' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $userId,
            'email' => 'sometimes|email|max:255|unique:users,email,' . $userId,
            'profile_picture' => 'sometimes|file|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}