<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessPaymentWebhook;
use App\Models\Contribution;
use App\Models\GuaranteeFundTransaction;
use App\Models\Payment;
use App\Models\WeddingRegistration;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
    ) {}

    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', Rule::enum(PaymentType::class)],
            'payable_id' => ['required', 'integer'],
        ]);

        $user = $request->user();
        $type = PaymentType::from($request->type);

        // Resolve payable and validate ownership + amount
        [$payable, $amount] = match ($type) {
            PaymentType::Contribution => $this->resolveContribution($user, $request->payable_id),
            PaymentType::WeddingRegistration => $this->resolveWedding($user, $request->payable_id),
            PaymentType::GuaranteeFee => $this->resolveGuaranteeFee($user, $request->payable_id),
        };

        if (!$payable) {
            return response()->json(['message' => 'العملية غير موجودة أو غير مصرح بها'], 404);
        }

        // Check if there's already a pending payment
        $existing = Payment::where('payable_type', get_class($payable))
            ->where('payable_id', $payable->id)
            ->where('status', 'processing')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return response()->json([
                'payment_uuid' => $existing->uuid,
                'checkout_url' => $existing->checkout_url,
                'fawry_ref_code' => $existing->fawry_ref_code,
                'merchant_ref' => $existing->merchant_ref,
                'message' => 'يوجد عملية دفع جارية بالفعل',
            ]);
        }

        $payment = $this->paymentService->createPayment($user, $type, $payable, $amount);
        $result = $this->paymentService->initiateCheckout($payment);

        return response()->json($result, 201);
    }

    public function status(Payment $payment): JsonResponse
    {
        // Verify ownership
        if ($payment->user_id !== request()->user()->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        // Refresh status from gateway if still pending/processing
        if ($payment->isPending() || $payment->status->value === 'processing') {
            $payment = $this->paymentService->verifyPayment($payment);
        }

        return response()->json([
            'uuid' => $payment->uuid,
            'status' => $payment->status->value,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'type' => $payment->type->value,
            'merchant_ref' => $payment->merchant_ref,
            'fawry_ref_code' => $payment->fawry_ref_code,
            'paid_at' => $payment->paid_at?->toISOString(),
            'expires_at' => $payment->expires_at?->toISOString(),
        ]);
    }

    public function fawryWebhook(Request $request): JsonResponse
    {
        ProcessPaymentWebhook::dispatch($request->all());

        return response()->json(['status' => 'received']);
    }

    public function callback(Request $request)
    {
        $ref = $request->query('ref');
        $payment = Payment::where('merchant_ref', $ref)->first();

        $uuid = $payment?->uuid ?? 'unknown';

        return redirect(config('app.url') . "/payment/status/{$uuid}");
    }

    private function resolveContribution($user, int $id): array
    {
        $contribution = Contribution::where('id', $id)
            ->where('status', 'pending')
            ->whereHas('member', fn ($q) => $q->where('user_id', $user->id))
            ->first();

        return [$contribution, $contribution?->amount ?? 0];
    }

    private function resolveWedding($user, int $id): array
    {
        $registration = WeddingRegistration::where('id', $id)
            ->where('user_id', $user->id)
            ->where('payment_status', 'pending')
            ->with('wedding')
            ->first();

        $amount = $registration?->wedding?->price_per_groom ?? 0;

        return [$registration, $amount];
    }

    private function resolveGuaranteeFee($user, int $id): array
    {
        // id is the circle_id here
        $circle = \App\Models\FundCircle::find($id);
        $member = \App\Models\CircleMember::where('circle_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$circle || !$member || $member->guarantee_deposit > 0) {
            return [null, 0];
        }

        $amount = $circle->monthly_amount * ($circle->guarantee_fee_percent / 100);

        // Check if there's already a pending transaction
        $transaction = GuaranteeFundTransaction::where('circle_id', $circle->id)
            ->where('member_id', $member->id)
            ->where('type', 'deposit')
            ->whereNull('payment_id')
            ->first();

        if (!$transaction) {
            $transaction = GuaranteeFundTransaction::create([
                'circle_id' => $circle->id,
                'member_id' => $member->id,
                'type' => 'deposit',
                'amount' => $amount,
                'reason' => 'رسم ضمان عند الانضمام — في انتظار الدفع',
            ]);
        }

        return [$transaction, $amount];
    }
}
