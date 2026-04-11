<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes', 'email', 'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'phone' => [
                'sometimes', 'string', 'max:20',
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'city_id' => 'sometimes|exists:cities,id',
            'date_of_birth' => 'sometimes|date|before:today',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'الاسم يجب ألا يتجاوز 255 حرفاً',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'city_id.exists' => 'المدينة غير موجودة',
            'date_of_birth.before' => 'تاريخ الميلاد غير صالح',
        ];
    }
}
