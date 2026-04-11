<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers' => 'required|array|size:10',
            'answers.*' => 'required|integer|min:0|max:3',
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required' => 'الإجابات مطلوبة',
            'answers.size' => 'يجب الإجابة على 10 أسئلة',
            'answers.*.required' => 'كل الأسئلة يجب الإجابة عليها',
            'answers.*.integer' => 'الإجابة يجب أن تكون رقماً',
            'answers.*.min' => 'إجابة غير صالحة',
            'answers.*.max' => 'إجابة غير صالحة',
        ];
    }
}
