<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|in:venue,photography,catering,furniture,clothing,other',
            'city_id' => 'required|exists:cities,id',
            'description' => 'nullable|string|max:1000',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المورد مطلوب',
            'category.required' => 'التصنيف مطلوب',
            'category.in' => 'التصنيف غير صالح',
            'city_id.required' => 'المدينة مطلوبة',
            'city_id.exists' => 'المدينة غير موجودة',
            'discount_percent.min' => 'نسبة الخصم يجب أن تكون 0 على الأقل',
            'discount_percent.max' => 'نسبة الخصم يجب ألا تتجاوز 100',
            'contact_email.email' => 'البريد الإلكتروني غير صالح',
        ];
    }
}
