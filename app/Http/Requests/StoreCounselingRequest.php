<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCounselingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:individual,group',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'نوع الجلسة مطلوب',
            'scheduled_at.required' => 'موعد الجلسة مطلوب',
            'scheduled_at.after' => 'موعد الجلسة يجب أن يكون في المستقبل',
        ];
    }
}
