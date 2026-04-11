<?php

namespace App\Services;

use App\Models\GroupWedding;
use App\Models\WeddingRegistration;

class RefundService
{
    /**
     * Calculate refund amount based on wedding date and refund policy
     */
    public function calculateRefund(WeddingRegistration $registration): array
    {
        $wedding = $registration->wedding;
        $daysUntilWedding = now()->diffInDays($wedding->wedding_date, false);

        // Cannot refund after wedding date
        if ($daysUntilWedding < 0) {
            return ['refund_percent' => 0, 'refund_amount' => 0, 'reason' => 'لا يمكن الاسترداد بعد تاريخ العرس'];
        }

        $amountPaid = $registration->amount_paid;

        // 100% refund if > refund_100_days before wedding
        if ($daysUntilWedding >= $wedding->refund_100_days) {
            return [
                'refund_percent' => 100,
                'refund_amount' => $amountPaid,
                'reason' => "استرداد كامل (أكثر من {$wedding->refund_100_days} يوم قبل الموعد)",
            ];
        }

        // 50% refund if > refund_50_days before wedding
        if ($daysUntilWedding >= $wedding->refund_50_days) {
            return [
                'refund_percent' => 50,
                'refund_amount' => round($amountPaid * 0.5, 2),
                'reason' => "استرداد 50% (أكثر من {$wedding->refund_50_days} يوم قبل الموعد)",
            ];
        }

        // 0% refund
        return [
            'refund_percent' => 0,
            'refund_amount' => 0,
            'reason' => "لا يمكن الاسترداد (أقل من {$wedding->refund_50_days} يوم قبل الموعد)",
        ];
    }

    /**
     * Process a refund request
     */
    public function requestRefund(WeddingRegistration $registration, string $reason): array
    {
        if ($registration->payment_status !== 'paid') {
            return ['success' => false, 'message' => 'لم يتم الدفع بعد — يمكنك إلغاء التسجيل مباشرة'];
        }

        if ($registration->refund_status !== 'none') {
            return ['success' => false, 'message' => 'يوجد طلب استرداد سابق'];
        }

        $refundCalc = $this->calculateRefund($registration);

        $registration->update([
            'refund_status' => 'requested',
            'refund_amount' => $refundCalc['refund_amount'],
            'refund_reason' => $reason,
        ]);

        AuditService::log('refund_requested', $registration, null, [
            'amount' => $refundCalc['refund_amount'],
            'percent' => $refundCalc['refund_percent'],
        ]);

        return [
            'success' => true,
            'message' => 'تم تقديم طلب الاسترداد',
            'refund_amount' => $refundCalc['refund_amount'],
            'refund_percent' => $refundCalc['refund_percent'],
            'policy' => $refundCalc['reason'],
        ];
    }
}
