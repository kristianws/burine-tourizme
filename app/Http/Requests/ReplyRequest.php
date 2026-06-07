<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
      $user = $this->user();

      if(!$user || $user->role !== 'bisnis_owner') {
        return false;
      }

      $review = $this->route('review');

      return $user->id === $review->destination->bisnis_owner_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
          'owner_reply' => 'required|string|min:5|max:1000',
        ];
    }
}
