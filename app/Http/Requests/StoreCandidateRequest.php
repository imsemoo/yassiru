<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'age' => 'required|integer|min:18|max:60',
            'education' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'marital_status' => 'required|in:single,divorced,widowed',
            'religiosity_level' => 'required|in:committed,moderate,learning',
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => ['required', 'string', 'regex:/^\+?[0-9]{10,15}$/'],
            'guardian_relation' => 'required|string|max:100',
            'preferences' => 'nullable|array',
            'recommender_notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المرشح مطلوب',
            'gender.required' => 'الجنس مطلوب',
            'age.min' => 'الحد الأدنى للعمر 18 سنة',
            'age.max' => 'الحد الأقصى للعمر 60 سنة',
            'marital_status.required' => 'الحالة الاجتماعية مطلوبة',
            'religiosity_level.required' => 'مستوى التدين مطلوب',
            'guardian_name.required' => 'اسم الولي مطلوب',
            'guardian_phone.required' => 'هاتف الولي مطلوب',
            'guardian_phone.regex' => 'صيغة رقم الهاتف غير صحيحة',
            'guardian_relation.required' => 'صلة القرابة مطلوبة',
        ];
    }
}
