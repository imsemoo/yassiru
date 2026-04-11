<?php

namespace App\Services;

use App\Models\CircleMember;
use App\Models\Contribution;
use App\Models\DigitalContract;
use App\Models\FundCircle;
use App\Models\Guarantor;
use App\Models\GuaranteeFundTransaction;
use App\Models\User;
use App\Notifications\ContributionOverdue;
use App\Notifications\GuarantorAlert;
use App\Notifications\MemberFrozen;

class GuaranteeService
{
    /**
     * Generate digital contract for a member joining a circle
     */
    public function generateContract(FundCircle $circle, User $user): DigitalContract
    {
        $terms = $this->buildContractTerms($circle);
        $penalties = $this->buildPenaltyTerms($circle);

        return DigitalContract::create([
            'circle_id' => $circle->id,
            'user_id' => $user->id,
            'terms' => $terms,
            'monthly_amount' => $circle->monthly_amount,
            'total_months' => $circle->cycle_months,
            'guarantee_fee_percent' => $circle->guarantee_fee_percent,
            'service_fee_percent' => $circle->service_fee_percent,
            'penalties' => $penalties,
        ]);
    }

    /**
     * Sign the digital contract
     */
    public function signContract(DigitalContract $contract, string $ip, string $userAgent, ?string $fingerprint = null): void
    {
        $signatureHash = hash('sha256', $contract->user_id . '|' . $contract->circle_id . '|' . now()->timestamp);

        $contract->update([
            'accepted' => true,
            'accepted_at' => now(),
            'ip_address' => $ip,
            'user_agent' => substr($userAgent, 0, 500),
            'device_fingerprint' => $fingerprint,
            'signature_hash' => $signatureHash,
        ]);

        $member = CircleMember::where('circle_id', $contract->circle_id)
            ->where('user_id', $contract->user_id)
            ->first();

        if ($member) {
            $member->update(['contract_signed' => true]);
        }

        AuditService::log('contract_signed', $contract);
    }

    /**
     * Register a guarantor for a circle member
     */
    public function registerGuarantor(CircleMember $member, array $data): Guarantor
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $guarantor = Guarantor::create([
            'circle_member_id' => $member->id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'national_id' => $data['national_id'],
            'relation' => $data['relation'],
            'confirmation_otp' => $otp,
            'otp_expires_at' => now()->addHours(24),
        ]);

        // SMS integration placeholder — will be connected with payment gateway phase
        \Log::info("Guarantor OTP for {$data['phone']}: {$otp}");

        return $guarantor;
    }

    /**
     * Confirm guarantor via OTP
     */
    public function confirmGuarantor(Guarantor $guarantor, string $otp, string $ip): array
    {
        if ($guarantor->confirmed) {
            return ['success' => false, 'message' => 'تم تأكيد الضامن مسبقاً'];
        }

        if ($guarantor->otp_expires_at->isPast()) {
            return ['success' => false, 'message' => 'انتهت صلاحية رمز التأكيد'];
        }

        if (!hash_equals($guarantor->confirmation_otp, $otp)) {
            return ['success' => false, 'message' => 'رمز التأكيد غير صحيح'];
        }

        $guarantor->update([
            'confirmed' => true,
            'confirmed_at' => now(),
            'ip_address' => $ip,
            'confirmation_otp' => null,
        ]);

        $guarantor->member->update(['has_guarantor' => true]);

        return ['success' => true, 'message' => 'تم تأكيد الضامن بنجاح'];
    }

    /**
     * Deposit guarantee fee into the circle's guarantee fund
     */
    public function depositGuaranteeFee(FundCircle $circle, CircleMember $member): GuaranteeFundTransaction
    {
        $feeAmount = $circle->monthly_amount * ($circle->guarantee_fee_percent / 100);

        $transaction = GuaranteeFundTransaction::create([
            'circle_id' => $circle->id,
            'member_id' => $member->id,
            'type' => 'deposit',
            'amount' => $feeAmount,
            'reason' => 'رسم ضمان عند الانضمام',
        ]);

        $member->update(['guarantee_deposit' => $feeAmount]);
        $circle->increment('guarantee_balance', $feeAmount);

        return $transaction;
    }

    /**
     * Cover a defaulted payment from the guarantee fund
     */
    public function coverDefault(FundCircle $circle, CircleMember $defaulter, Contribution $contribution): array
    {
        $amount = $contribution->amount;

        if ($circle->guarantee_balance < $amount) {
            return ['success' => false, 'message' => 'رصيد صندوق الضمان غير كافٍ'];
        }

        GuaranteeFundTransaction::create([
            'circle_id' => $circle->id,
            'member_id' => $defaulter->id,
            'type' => 'coverage',
            'amount' => $amount,
            'reason' => "تغطية تخلف العضو عن الجولة {$contribution->round_number}",
        ]);

        $circle->decrement('guarantee_balance', $amount);

        $contribution->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_ref' => 'GUARANTEE_FUND_COVERAGE',
        ]);

        AuditService::log('guarantee_fund_coverage', $contribution, null, [
            'defaulter_id' => $defaulter->id,
            'amount' => $amount,
        ]);

        return ['success' => true, 'message' => 'تم تغطية المبلغ من صندوق الضمان'];
    }

    /**
     * Check if user can join more circles
     */
    public function canJoinCircle(User $user): array
    {
        if ($user->is_banned_from_circles) {
            return ['allowed' => false, 'reason' => 'تم حظرك من الانضمام للحلقات'];
        }

        if ($user->active_circles_count >= $user->max_active_circles) {
            return ['allowed' => false, 'reason' => "لا يمكنك الانضمام لأكثر من {$user->max_active_circles} حلقات نشطة"];
        }

        if (!$user->is_verified) {
            return ['allowed' => false, 'reason' => 'يجب توثيق هويتك أولاً'];
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * Handle late payment escalation
     */
    public function handleLatePayment(FundCircle $circle, CircleMember $member, Contribution $contribution): string
    {
        $daysLate = now()->diffInDays($contribution->due_date);

        if ($daysLate <= 0) {
            return 'not_late';
        }

        // Stage 1: Warning (after grace period)
        if ($daysLate > $circle->late_grace_days && $daysLate <= $circle->freeze_after_days) {
            $contribution->update(['status' => 'late']);
            $member->increment('late_count');

            // Notify the member
            $member->user->notify(new ContributionOverdue($contribution, $circle, 'warning'));

            // Notify guarantor
            $guarantor = Guarantor::where('circle_member_id', $member->id)
                ->where('confirmed', true)
                ->first();
            if ($guarantor) {
                $member->user->notify(new GuarantorAlert(
                    $circle,
                    $member->user->name,
                    $contribution->amount,
                    $circle->currency,
                ));
            }

            return 'warning';
        }

        // Stage 2: Freeze (after freeze days)
        if ($daysLate > $circle->freeze_after_days && $daysLate <= $circle->remove_after_days) {
            $member->update(['status' => 'frozen']);

            // Notify the frozen member
            $member->user->notify(new ContributionOverdue($contribution, $circle, 'frozen'));

            // Notify all other circle members
            $otherMembers = CircleMember::where('circle_id', $circle->id)
                ->where('id', '!=', $member->id)
                ->where('status', 'active')
                ->with('user')
                ->get();

            foreach ($otherMembers as $m) {
                $m->user->notify(new MemberFrozen($circle, $member->user->name));
            }

            return 'frozen';
        }

        // Stage 3: Remove + Ban (after remove days)
        if ($daysLate > $circle->remove_after_days) {
            if ($member->status === 'removed') {
                return 'already_removed'; // Idempotency guard
            }
            $member->update(['status' => 'removed', 'is_banned' => true]);
            $member->user->is_banned_from_circles = true;
            $member->user->save();
            if ($member->user->active_circles_count > 0) {
                $member->user->decrement('active_circles_count');
            }

            // Cover from guarantee fund
            $this->coverDefault($circle, $member, $contribution);

            AuditService::log('member_removed_for_default', $member, null, [
                'circle_id' => $circle->id,
                'days_late' => $daysLate,
            ]);

            return 'removed';
        }

        return 'grace';
    }

    private function buildContractTerms(FundCircle $circle): string
    {
        $total = $circle->monthly_amount * $circle->max_members;

        return implode("\n", [
            "عقد التزام — حلقة التيسير: {$circle->name}",
            "",
            "1. أتعهد بدفع مبلغ {$circle->monthly_amount} {$circle->currency} شهرياً لمدة {$circle->cycle_months} شهراً.",
            "2. إجمالي المبلغ المستحق عند دوري: {$total} {$circle->currency}.",
            "3. رسوم الضمان: {$circle->guarantee_fee_percent}% تُدفع عند الانضمام وتُسترد عند إكمال الحلقة بنجاح.",
            "4. رسوم الخدمة: {$circle->service_fee_percent}% من كل مساهمة شهرية.",
            "5. يتم الخصم تلقائياً من وسيلة الدفع المسجلة.",
            "6. أوافق على تقديم ضامن يتعهد بسداد المبالغ في حال تعثري.",
            "7. هذا العقد ملزم من لحظة التوقيع الإلكتروني.",
        ]);
    }

    private function buildPenaltyTerms(FundCircle $circle): string
    {
        return implode("\n", [
            "بنود العقوبات:",
            "",
            "1. تأخر {$circle->late_grace_days} أيام: تحذير رسمي + إشعار الضامن.",
            "2. تأخر {$circle->freeze_after_days} أيام: تجميد الحساب على المنصة + إشعار جميع الأعضاء.",
            "3. تأخر {$circle->remove_after_days} يوم: استبعاد نهائي + حظر من جميع الحلقات المستقبلية.",
            "4. المبالغ المتخلف عنها تُغطى من صندوق الضمان.",
            "5. يحق للمنصة اتخاذ الإجراءات القانونية لتحصيل المبالغ المستحقة.",
            "6. المتخلف يفقد حقه في الحصول على دوره في الحلقة.",
        ]);
    }
}
