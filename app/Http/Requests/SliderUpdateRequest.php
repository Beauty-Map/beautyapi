<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderUpdateRequest extends FormRequest
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
            'main_title' => 'required|string',
            'image' => 'required|string',
            'sub_title' => 'nullable|string',
            'description' => 'nullable|string',
            'link_url' => 'nullable|string',
            'link_title' => 'nullable|string',
        ];
    }
}
