<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeddingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id' => 'required|exists:cities,id',
            'title' => 'required|string|max:255',
            'venue_name' => 'required|string|max:255',
            'wedding_date' => 'required|date|after:today',
            'max_grooms' => 'required|integer|min:5|max:100',
            'price_per_groom' => 'required|numeric|min:100',
            'original_price' => 'required|numeric|min:100',
            'description' => 'nullable|string|max:2000',
            'registration_deadline' => 'required|date|before:wedding_date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان العرس مطلوب',
            'venue_name.required' => 'اسم المكان مطلوب',
            'wedding_date.after' => 'تاريخ العرس يجب أن يكون في المستقبل',
            'registration_deadline.before' => 'آخر موعد للتسجيل يجب أن يكون قبل تاريخ العرس',
        ];
    }
}
