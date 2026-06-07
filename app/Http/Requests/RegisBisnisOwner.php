<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisBisnisOwner extends FormRequest
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
        return [
          'user_id' => 'required|exists:users,id',
          'nik' => 'required|string|unique:bisnis_owners,nik',
          'ktp_photo' => 'required|string|accepted|mimes:jpg,jpeg,png|max:2048',
          'nib' => 'required|string|unique:bisnis_owners,nib'          
        ];
    }
}
