<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileUpdateRequest extends FormRequest
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
            'city_id' => 'nullable|exists:cities,id',
            'birth_date' => 'nullable|date',
            'work_hours' => 'nullable|array',
            'work_hours.*' => 'required|array',
            'work_hours.*.day_index' => 'required|min:1|max:7',
            'work_hours.*.start_hour' => 'required|date_format:H:i',
            'work_hours.*.end_hour' => 'required|date_format:H:i|after:start_hour',
        ];
    }
}
