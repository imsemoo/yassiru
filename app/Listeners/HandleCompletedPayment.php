<?php

namespace App\Listeners;

use App\Enums\PaymentType;
use App\Events\PaymentCompleted;
use App\Models\CircleMember;
use App\Models\Contribution;
use App\Models\FundCircle;
use App\Models\GuaranteeFundTransaction;
use App\Models\WeddingRegistration;
use App\Notifications\PaymentConfirmed;
use App\Services\AuditService;
use App\Services\TrustScoreService;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleCompletedPayment implements ShouldQueue
{
    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment->load('user', 'payable');

        match ($payment->type) {
            PaymentType::Contribution => $this->handleContribution($payment),
            PaymentType::WeddingRegistration => $this->handleWedding($payment),
            PaymentType::GuaranteeFee => $this->handleGuaranteeFee($payment),
        };

        // Send confirmation notification
        $payment->user->notify(new PaymentConfirmed($payment));
    }

    private function handleContribution($payment): void
    {
        $contribution = $payment->payable;
        if (!$contribution instanceof Contribution) {
            return;
        }

        $contribution->update([
            'status' => 'paid',
            'payment_ref' => $payment->merchant_ref,
            'paid_at' => now(),
            'payment_id' => $payment->id,
        ]);

        $member = CircleMember::find($contribution->member_id);
        if ($member) {
            $member->increment('total_contributed', $contribution->amount);
        }

        // Trust score
        app(TrustScoreService::class)->addPoints($payment->user, 'monthly_payment_on_time');

        AuditService::log('contribution_paid', $contribution, null, [
            'circle_id' => $contribution->circle_id,
            'amount' => $contribution->amount,
            'payment_id' => $payment->id,
        ]);

        // Check round completion
        $circle = FundCircle::find($contribution->circle_id);
        if ($circle) {
            $pendingCount = Contribution::where('circle_id', $circle->id)
                ->where('round_number', $circle->current_round)
                ->where('status', '!=', 'paid')
                ->count();

            if ($pendingCount === 0) {
                $recipient = CircleMember::where('circle_id', $circle->id)
                    ->where('payout_order', $circle->current_round)
                    ->first();

                if ($recipient) {
                    \App\Jobs\IssuePayout::dispatch($circle, $recipient);
                }
            }
        }
    }

    private function handleWedding($payment): void
    {
        $registration = $payment->payable;
        if (!$registration instanceof WeddingRegistration) {
            return;
        }

        $registration->update([
            'payment_status' => 'paid',
            'payment_ref' => $payment->merchant_ref,
            'amount_paid' => $payment->amount,
            'payment_id' => $payment->id,
        ]);

        AuditService::log('wedding_payment_confirmed', $registration, null, [
            'amount' => $payment->amount,
            'payment_id' => $payment->id,
        ]);
    }

    private function handleGuaranteeFee($payment): void
    {
        $transaction = $payment->payable;
        if (!$transaction instanceof GuaranteeFundTransaction) {
            return;
        }

        $transaction->update(['payment_id' => $payment->id]);

        $member = CircleMember::find($transaction->member_id);
        $circle = FundCircle::find($transaction->circle_id);

        if ($member) {
            $member->update(['guarantee_deposit' => $payment->amount]);
        }

        if ($circle) {
            $circle->increment('guarantee_balance', $payment->amount);
        }
    }
}
