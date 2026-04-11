<?php

namespace App\Contracts;

use App\Models\Payment;

interface PaymentProviderInterface
{
    /**
     * Initiate a payment and return checkout data.
     *
     * @return array{checkout_url: string|null, merchant_ref: string, fawry_ref_code: string|null, payment_data: array}
     */
    public function initiate(Payment $payment): array;

    /**
     * Verify payment status with the gateway.
     *
     * @return array{status: string, gateway_ref: string|null, raw: array}
     */
    public function verify(Payment $payment): array;

    /**
     * Refund a completed payment.
     *
     * @return array{success: bool, refund_ref: string|null, raw: array}
     */
    public function refund(Payment $payment, float $amount): array;

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(array $payload): bool;
}
