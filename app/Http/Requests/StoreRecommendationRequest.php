<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecommendationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'male_candidate_id' => 'required|exists:candidates,id',
            'female_candidate_id' => 'required|exists:candidates,id',
            'reason' => 'required|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'male_candidate_id.required' => 'المرشح الذكر مطلوب',
            'male_candidate_id.exists' => 'المرشح الذكر غير موجود',
            'female_candidate_id.required' => 'المرشحة مطلوبة',
            'female_candidate_id.exists' => 'المرشحة غير موجودة',
            'reason.required' => 'سبب التوصية مطلوب',
            'reason.max' => 'سبب التوصية يجب ألا يتجاوز 2000 حرف',
        ];
    }
}
