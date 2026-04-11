<?php

namespace App\Services\Payment\Providers;

use App\Contracts\PaymentProviderInterface;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FawryPaymentProvider implements PaymentProviderInterface
{
    private string $merchantCode;
    private string $securityKey;
    private string $baseUrl;
    private string $returnUrl;
    private int $expiryHours;

    public function __construct()
    {
        $this->merchantCode = config('services.fawry.merchant_code', '');
        $this->securityKey = config('services.fawry.security_key', '');
        $this->baseUrl = config('services.fawry.base_url', 'https://atfawry.fawrystaging.com');
        $this->returnUrl = config('services.fawry.return_url', config('app.url') . '/payment/callback');
        $this->expiryHours = (int) config('services.fawry.expiry_hours', 48);
    }

    public function initiate(Payment $payment): array
    {
        $user = $payment->user;
        $expiry = now()->addHours($this->expiryHours)->timestamp * 1000; // milliseconds

        $chargeItem = [
            'itemId' => $payment->merchant_ref,
            'description' => $this->getDescription($payment),
            'price' => (float) $payment->amount,
            'quantity' => 1,
        ];

        $signature = $this->computeChargeSignature(
            $payment->merchant_ref,
            (string) $user->id,
            $chargeItem['itemId'],
            (float) $payment->amount,
        );

        $payload = [
            'merchantCode' => $this->merchantCode,
            'merchantRefNum' => $payment->merchant_ref,
            'customerProfileId' => (string) $user->id,
            'customerName' => $user->name,
            'customerMobile' => $user->phone ?? '',
            'customerEmail' => $user->email,
            'chargeItems' => [$chargeItem],
            'returnUrl' => $this->returnUrl . '?ref=' . $payment->merchant_ref,
            'authCaptureModePayment' => false,
            'paymentExpiry' => $expiry,
            'signature' => $signature,
            'language' => 'ar-eg',
        ];

        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/ECommerceWeb/Fawry/payments/charge", $payload);

            $data = $response->json();

            if ($response->successful() && isset($data['type']) && $data['type'] === 'ChargeResponse') {
                return [
                    'checkout_url' => $data['redirectUrl'] ?? null,
                    'merchant_ref' => $payment->merchant_ref,
                    'fawry_ref_code' => $data['referenceNumber'] ?? null,
                    'payment_data' => $data,
                ];
            }

            Log::warning('Fawry initiate failed', ['response' => $data, 'payment' => $payment->id]);

            return [
                'checkout_url' => null,
                'merchant_ref' => $payment->merchant_ref,
                'fawry_ref_code' => null,
                'payment_data' => $data ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Fawry initiate exception', ['error' => $e->getMessage(), 'payment' => $payment->id]);

            return [
                'checkout_url' => null,
                'merchant_ref' => $payment->merchant_ref,
                'fawry_ref_code' => null,
                'payment_data' => ['error' => $e->getMessage()],
            ];
        }
    }

    public function verify(Payment $payment): array
    {
        $signature = hash('sha256', $this->merchantCode . $payment->merchant_ref . $this->securityKey);

        try {
            $response = Http::timeout(15)
                ->get("{$this->baseUrl}/ECommerceWeb/Fawry/payments/status/v2", [
                    'merchantCode' => $this->merchantCode,
                    'merchantRefNumber' => $payment->merchant_ref,
                    'signature' => $signature,
                ]);

            $data = $response->json();

            return [
                'status' => $this->mapFawryStatus($data['paymentStatus'] ?? 'UNKNOWN'),
                'gateway_ref' => $data['referenceNumber'] ?? null,
                'raw' => $data ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Fawry verify exception', ['error' => $e->getMessage(), 'payment' => $payment->id]);

            return [
                'status' => PaymentStatus::Pending->value,
                'gateway_ref' => null,
                'raw' => ['error' => $e->getMessage()],
            ];
        }
    }

    public function refund(Payment $payment, float $amount): array
    {
        $signature = hash('sha256', $this->merchantCode . $payment->gateway_ref . number_format($amount, 2, '.', '') . $this->securityKey);

        try {
            $response = Http::timeout(15)
                ->post("{$this->baseUrl}/ECommerceWeb/Fawry/payments/refund", [
                    'merchantCode' => $this->merchantCode,
                    'referenceNumber' => $payment->gateway_ref,
                    'refundAmount' => $amount,
                    'signature' => $signature,
                ]);

            $data = $response->json();

            return [
                'success' => $response->successful() && ($data['statusCode'] ?? 0) === 200,
                'refund_ref' => $data['referenceNumber'] ?? null,
                'raw' => $data ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Fawry refund exception', ['error' => $e->getMessage(), 'payment' => $payment->id]);

            return ['success' => false, 'refund_ref' => null, 'raw' => ['error' => $e->getMessage()]];
        }
    }

    public function verifyWebhookSignature(array $payload): bool
    {
        $fawryRef = $payload['fawryRefNumber'] ?? '';
        $merchantRef = $payload['merchantRefNumber'] ?? '';
        $amount = $payload['paymentAmount'] ?? 0;
        $orderAmount = $payload['orderAmount'] ?? 0;
        $status = $payload['orderStatus'] ?? '';
        $method = $payload['paymentMethod'] ?? '';
        $paymentRef = $payload['paymentRefrenceNumber'] ?? '';

        $raw = $fawryRef . $merchantRef . number_format($amount, 2, '.', '')
            . number_format($orderAmount, 2, '.', '') . $status . $method . $paymentRef . $this->securityKey;

        $computed = hash('sha256', $raw);
        $received = $payload['messageSignature'] ?? '';

        return hash_equals($computed, $received);
    }

    private function computeChargeSignature(string $merchantRef, string $customerId, string $itemId, float $amount): string
    {
        $raw = $this->merchantCode . $merchantRef . $customerId . $itemId
            . number_format($amount, 2, '.', '') . $this->securityKey;

        return hash('sha256', $raw);
    }

    private function mapFawryStatus(string $fawryStatus): string
    {
        return match (strtoupper($fawryStatus)) {
            'PAID' => PaymentStatus::Paid->value,
            'NEW', 'UNPAID' => PaymentStatus::Pending->value,
            'EXPIRED' => PaymentStatus::Expired->value,
            'FAILED', 'CANCELLED' => PaymentStatus::Failed->value,
            'REFUNDED' => PaymentStatus::Refunded->value,
            default => PaymentStatus::Pending->value,
        };
    }

    private function getDescription(Payment $payment): string
    {
        return match ($payment->type->value) {
            'contribution' => 'مساهمة صندوق يسّرو',
            'guarantee_fee' => 'رسوم ضمان يسّرو',
            'wedding' => 'تسجيل عرس جماعي يسّرو',
            default => 'دفع يسّرو',
        };
    }
}
