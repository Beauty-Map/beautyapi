<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserUpdateRequest extends FormRequest
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
            'full_name' => 'required|string|min:3',
            'national_code' => 'nullable|string|min:3',
            'phone_number' => 'nullable|string|min:3',
            'city_id' => 'nullable|exists:cities,id',
            'birth_date' => 'nullable|date',
            'work_hours' => 'nullable|array',
            'coins' => 'numeric|min:0',
        ];
    }
}
