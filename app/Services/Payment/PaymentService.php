<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProviderInterface;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Events\PaymentCompleted;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private PaymentProviderInterface $provider,
    ) {}

    public function createPayment(User $user, PaymentType $type, Model $payable, float $amount, array $metadata = []): Payment
    {
        return Payment::create([
            'user_id' => $user->id,
            'type' => $type,
            'payable_type' => get_class($payable),
            'payable_id' => $payable->id,
            'amount' => $amount,
            'metadata' => $metadata,
        ]);
    }

    public function initiateCheckout(Payment $payment): array
    {
        $result = $this->provider->initiate($payment);

        $payment->update([
            'checkout_url' => $result['checkout_url'],
            'fawry_ref_code' => $result['fawry_ref_code'],
            'gateway_response' => $result['payment_data'],
            'status' => PaymentStatus::Processing,
            'expires_at' => now()->addHours((int) config('services.fawry.expiry_hours', 48)),
        ]);

        return [
            'payment_uuid' => $payment->uuid,
            'checkout_url' => $result['checkout_url'],
            'fawry_ref_code' => $result['fawry_ref_code'],
            'merchant_ref' => $payment->merchant_ref,
        ];
    }

    public function verifyPayment(Payment $payment): Payment
    {
        $result = $this->provider->verify($payment);
        $newStatus = PaymentStatus::from($result['status']);

        if ($payment->status === $newStatus) {
            return $payment;
        }

        DB::transaction(function () use ($payment, $newStatus, $result) {
            $payment = Payment::lockForUpdate()->find($payment->id);

            if ($payment->isPaid()) {
                return; // already processed
            }

            $payment->update([
                'status' => $newStatus,
                'gateway_ref' => $result['gateway_ref'] ?? $payment->gateway_ref,
                'gateway_response' => $result['raw'],
                'paid_at' => $newStatus === PaymentStatus::Paid ? now() : $payment->paid_at,
            ]);

            if ($newStatus === PaymentStatus::Paid) {
                event(new PaymentCompleted($payment));
            }
        });

        return $payment->fresh();
    }

    public function processRefund(Payment $payment, float $amount): array
    {
        $result = $this->provider->refund($payment, $amount);

        if ($result['success']) {
            $totalRefund = $payment->refund_amount + $amount;
            $payment->update([
                'refund_amount' => $totalRefund,
                'status' => $totalRefund >= $payment->amount
                    ? PaymentStatus::Refunded
                    : PaymentStatus::PartialRefund,
            ]);
        }

        return $result;
    }

    public function handleWebhookPayload(array $payload): void
    {
        if (!$this->provider->verifyWebhookSignature($payload)) {
            \Log::warning('Fawry webhook signature verification failed', $payload);
            throw new \RuntimeException('Webhook signature verification failed');
        }

        $merchantRef = $payload['merchantRefNumber'] ?? null;
        if (!$merchantRef) {
            return;
        }

        $payment = Payment::where('merchant_ref', $merchantRef)->first();
        if (!$payment || $payment->isPaid()) {
            return;
        }

        // Verify with Fawry API directly (don't trust webhook alone)
        $this->verifyPayment($payment);
    }
}
