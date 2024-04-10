<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'coins' => 'required|numeric|min:0',
            'portfolio_count' => 'required|numeric|min:0',
            'laddering_count' => 'required|numeric|min:0',
            'star_count' => 'required|numeric|min:1|max:5',
            'has_blue_tick' => 'required|boolean',
            'image_upload_count' => 'required|numeric|min:0',
            'has_discount' => 'required|boolean',
        ];
    }
}
