<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PortfolioUpdateRequest extends FormRequest
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
            'title' => 'required|string|min:3|max:40',
            'description' => 'required|string|max:200|min:3',
            'maintenance' => 'nullable|string|max:200',
            'service_id' => 'required|exists:services,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'required|numeric|min:0',
            'has_tel' => 'required|boolean',
            'has_phone_number' => 'required|boolean',
            'second_phone_number' => Rule::requiredIf(!$this->get('has_tel') && !$this->get('has_phone_number')),
            'images' => 'required|array',
            'images.*' => 'required|string',
        ];
    }
}
