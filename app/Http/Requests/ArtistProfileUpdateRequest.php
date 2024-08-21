<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArtistProfileUpdateRequest extends FormRequest
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
            'phone_number' => 'required|string|min:3',
            'full_name' => 'required|string|min:3',
            'national_code' => 'nullable|string',
            'tel_number' => 'nullable|string',
            'address' => 'nullable|string',
            'avatar' => 'nullable|string',
            'bio' => 'nullable|string',
            'location' => 'nullable|array',
            'social_media' => 'nullable|array',
            'is_all_day_open' => 'boolean',
            'is_closed' => 'boolean',
            'work_on_holidays' => 'boolean',
            'city_id' => 'nullable|exists:cities,id',
            'birth_date' => 'nullable|date',
            'documents' => 'nullable|array',
            'documents.*' => 'string',
            'work_hours' => 'nullable|array',
            'work_hours.*' => 'required|array',
            'work_hours.*.day_index' => 'required|min:1|max:7',
            'work_hours.*.start_hour' => 'required|date_format:H:i',
            'work_hours.*.end_hour' => 'required|date_format:H:i|after:start_hour',
        ];
    }
}
