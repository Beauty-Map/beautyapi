<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'own' => 'required|numeric|min:0|max:100',
            'first' => 'required|numeric|min:0|max:100',
            'second' => 'required|numeric|min:0|max:100',
            'third' => 'required|numeric|min:0|max:100',
            'forth' => 'required|numeric|min:0|max:100',
            'lock_all' => 'required|boolean',
        ];
    }
}
