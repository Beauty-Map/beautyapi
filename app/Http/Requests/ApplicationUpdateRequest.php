<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationUpdateRequest extends FormRequest
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
            'app_id' => 'required|string|unique:applications,app_id,'. $this->user()->id,
            'app_name' => 'required|string|unique:applications,app_name,'. $this->user()->id,
            'app_link' => 'required|string|unique:applications,app_link,'. $this->user()->id,
        ];
    }
}
