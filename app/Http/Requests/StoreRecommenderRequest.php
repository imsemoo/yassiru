<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecommenderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:imam,teacher,relative,community_leader',
            'institution' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'نوع المعرّف مطلوب',
            'type.in' => 'نوع المعرّف غير صالح',
            'institution.max' => 'اسم المؤسسة يجب ألا يتجاوز 255 حرفاً',
            'bio.max' => 'السيرة الذاتية يجب ألا تتجاوز 1000 حرف',
        ];
    }
}
