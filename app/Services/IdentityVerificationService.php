<?php

namespace App\Services;

use App\Models\User;

class IdentityVerificationService
{
    /**
     * Send OTP for phone verification
     */
    public function sendPhoneOtp(User $user): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        cache()->put("phone_otp:{$user->id}", [
            'otp' => $otp,
            'phone' => $user->phone,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ], 700); // Cache TTL slightly longer than logical expiry

        // TODO: Integrate real SMS provider (Twilio, Vonage, etc.)
        // SmsService::send($user->phone, "رمز التحقق من يسّرو: {$otp}");

        return $otp;
    }

    /**
     * Verify phone OTP
     */
    public function verifyPhoneOtp(User $user, string $otp): array
    {
        $cached = cache()->get("phone_otp:{$user->id}");

        if (!$cached) {
            return ['success' => false, 'message' => 'انتهت صلاحية رمز التحقق. أعد الإرسال'];
        }

        if ($cached['attempts'] >= 5) {
            cache()->forget("phone_otp:{$user->id}");
            return ['success' => false, 'message' => 'تم تجاوز عدد المحاولات المسموح. أعد الإرسال'];
        }

        // Check absolute expiration (not refreshable)
        if (isset($cached['expires_at']) && now()->timestamp > $cached['expires_at']) {
            cache()->forget("phone_otp:{$user->id}");
            return ['success' => false, 'message' => 'انتهت صلاحية رمز التحقق. أعد الإرسال'];
        }

        if (!hash_equals($cached['otp'], $otp)) {
            $cached['attempts']++;
            // Keep cache alive but don't extend the logical expiration
            cache()->put("phone_otp:{$user->id}", $cached, 700); // buffer beyond expires_at
            return ['success' => false, 'message' => 'رمز التحقق غير صحيح'];
        }

        cache()->forget("phone_otp:{$user->id}");
        return ['success' => true, 'message' => 'تم التحقق بنجاح'];
    }

    /**
     * Check if national ID is already used by another account
     */
    public function isNationalIdUnique(string $nationalId, int $excludeUserId = 0): bool
    {
        return !User::where('national_id', $nationalId)
            ->where('id', '!=', $excludeUserId)
            ->exists();
    }

    /**
     * Process identity document verification
     */
    public function verifyIdentity(User $user, string $nationalId, ?string $imagePath = null): array
    {
        // Check uniqueness
        if (!$this->isNationalIdUnique($nationalId, $user->id)) {
            return [
                'success' => false,
                'message' => 'رقم الهوية مستخدم بالفعل في حساب آخر',
            ];
        }

        // Validate national ID format (Egyptian: 14 digits)
        if (!$this->validateNationalIdFormat($nationalId)) {
            return [
                'success' => false,
                'message' => 'صيغة رقم الهوية غير صحيحة',
            ];
        }

        // Store national ID temporarily for verification
        $user->national_id = $nationalId;
        $user->is_verified = true;
        $user->save();

        // Delete uploaded image immediately after verification (privacy)
        if ($imagePath) {
            \Storage::disk('local')->delete($imagePath);
        }

        // Award trust points
        app(TrustScoreService::class)->addPoints($user, 'identity_verified');

        AuditService::log('identity_verified', $user);

        // Schedule national ID deletion after 24 hours (privacy)
        \App\Jobs\DeleteNationalId::dispatch($user)->delay(now()->addHours(24));

        return [
            'success' => true,
            'message' => 'تم توثيق هويتك بنجاح. سيتم حذف بيانات الهوية خلال 24 ساعة',
        ];
    }

    /**
     * Check login from new device
     */
    public function isNewDevice(User $user, string $ip, string $userAgent): bool
    {
        $deviceHash = md5($ip . $userAgent);
        $knownDevices = cache()->get("user_devices:{$user->id}", []);

        if (in_array($deviceHash, $knownDevices)) {
            return false;
        }

        // Add device to known list (keep last 10)
        $knownDevices[] = $deviceHash;
        $knownDevices = array_slice($knownDevices, -10);
        cache()->put("user_devices:{$user->id}", $knownDevices, now()->addDays(90));

        return true;
    }

    /**
     * Validate Egyptian national ID format
     */
    private function validateNationalIdFormat(string $id): bool
    {
        // Egyptian: 14 digits starting with 2 or 3
        if (preg_match('/^[23]\d{13}$/', $id)) {
            return true;
        }

        // Saudi: 10 digits starting with 1 or 2
        if (preg_match('/^[12]\d{9}$/', $id)) {
            return true;
        }

        // Generic: 8-20 alphanumeric
        if (preg_match('/^[A-Za-z0-9]{8,20}$/', $id)) {
            return true;
        }

        return false;
    }
}
