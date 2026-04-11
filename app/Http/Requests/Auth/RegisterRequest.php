<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{10,15}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'gender' => ['required', 'in:male,female'],
            'city_id' => ['required', 'exists:cities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مستخدم مسبقاً',
            'phone.regex' => 'رقم الهاتف غير صالح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.letters' => 'كلمة المرور يجب أن تحتوي على حروف',
            'password.numbers' => 'كلمة المرور يجب أن تحتوي على أرقام',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'gender.required' => 'الجنس مطلوب',
            'city_id.required' => 'المدينة مطلوبة',
            'city_id.exists' => 'المدينة غير موجودة',
        ];
    }
}
