<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCircleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'max_members' => 'required|integer|min:5|max:30',
            'monthly_amount' => 'required|numeric|min:100|max:50000',
            'currency' => 'required|string|max:10',
            'payout_method' => 'required|in:lottery,priority,schedule',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الحلقة مطلوب',
            'city_id.required' => 'المدينة مطلوبة',
            'max_members.min' => 'الحد الأدنى للأعضاء 5',
            'max_members.max' => 'الحد الأقصى للأعضاء 30',
            'monthly_amount.min' => 'الحد الأدنى للمبلغ الشهري 100',
            'monthly_amount.max' => 'الحد الأقصى للمبلغ الشهري 50,000',
            'payout_method.required' => 'طريقة الصرف مطلوبة',
        ];
    }
}
