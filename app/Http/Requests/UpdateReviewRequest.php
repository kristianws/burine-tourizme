<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{


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
      'review_id' => 'required|exists:reviews,id',
      'rating' => 'required|integer|min:1|max:5',
      'description' => 'nullable|string',
    ];
  }
}
