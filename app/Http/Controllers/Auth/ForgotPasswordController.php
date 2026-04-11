<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Always return same message to prevent user enumeration
        Password::sendResetLink($request->only('email'));

        return response()->json(['message' => 'إذا كان البريد مسجلاً لدينا، سيتم إرسال رابط إعادة التعيين']);
    }
}
