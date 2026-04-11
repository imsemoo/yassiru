<?php

namespace App\Services;

use App\Jobs\IssuePayout;
use App\Jobs\SendCircleStartedNotifications;
use App\Models\CircleMember;
use App\Models\Contribution;
use App\Models\FundCircle;
use App\Models\Payout;
use App\Services\AuditService;
use App\Services\GuaranteeService;
use App\Services\TrustScoreService;
use Illuminate\Support\Facades\DB;

class FundCircleService
{
    public function createCircle(array $data, int $userId): FundCircle
    {
        $circle = FundCircle::create([
            'name' => $data['name'],
            'city_id' => $data['city_id'] ?? null,
            'created_by' => $userId,
            'max_members' => $data['max_members'],
            'monthly_amount' => $data['monthly_amount'],
            'currency' => $data['currency'] ?? 'EGP',
            'cycle_months' => $data['max_members'],
            'payout_method' => $data['payout_method'] ?? 'schedule',
        ]);

        // Creator joins as first member
        CircleMember::create([
            'circle_id' => $circle->id,
            'user_id' => $userId,
            'payout_order' => 1,
            'joined_at' => now(),
        ]);

        return $circle;
    }

    public function joinCircle(FundCircle $circle, int $userId): array
    {
        $user = \App\Models\User::findOrFail($userId);

        // Check ban + circle limit
        $guaranteeService = app(GuaranteeService::class);
        $canJoin = $guaranteeService->canJoinCircle($user);
        if (!$canJoin['allowed']) {
            return ['success' => false, 'message' => $canJoin['reason']];
        }

        if ($circle->status !== 'forming') {
            return ['success' => false, 'message' => 'الحلقة غير متاحة للانضمام'];
        }

        return DB::transaction(function () use ($circle, $user, $userId, $guaranteeService) {
            // Lock circle to prevent race condition on member count
            $circle = FundCircle::lockForUpdate()->find($circle->id);

            $membersCount = CircleMember::where('circle_id', $circle->id)->count();

            if ($membersCount >= $circle->max_members) {
                return ['success' => false, 'message' => 'الحلقة مكتملة العدد'];
            }

            if (CircleMember::where('circle_id', $circle->id)->where('user_id', $userId)->exists()) {
                return ['success' => false, 'message' => 'أنت عضو بالفعل في هذه الحلقة'];
            }

            $member = CircleMember::create([
                'circle_id' => $circle->id,
                'user_id' => $userId,
                'payout_order' => $membersCount + 1,
                'joined_at' => now(),
            ]);

            $user->increment('active_circles_count');

            // Generate digital contract
            $contract = $guaranteeService->generateContract($circle, $user);

            // Auto-start only if full AND all members signed contracts + have guarantors
            if (($membersCount + 1) >= $circle->max_members) {
                $this->checkReadyToStart($circle);
            }

            return [
                'success' => true,
                'message' => 'تم الانضمام. يرجى توقيع العقد وتسجيل ضامن لبدء الحلقة',
                'contract_id' => $contract->id,
                'requires_guarantor' => $circle->requires_guarantor,
            ];
        });
    }

    /**
     * Check if circle is ready to start (all members signed + have guarantors)
     */
    public function checkReadyToStart(FundCircle $circle): bool
    {
        $membersCount = CircleMember::where('circle_id', $circle->id)->count();

        if ($membersCount < $circle->max_members) {
            return false;
        }

        $allSigned = CircleMember::where('circle_id', $circle->id)
            ->where('contract_signed', false)
            ->doesntExist();

        $allGuarantored = !$circle->requires_guarantor || CircleMember::where('circle_id', $circle->id)
            ->where('has_guarantor', false)
            ->doesntExist();

        if ($allSigned && $allGuarantored) {
            $this->startCircle($circle);
            return true;
        }

        return false;
    }

    public function startCircle(FundCircle $circle): void
    {
        $circle->update(['status' => 'active', 'started_at' => now(), 'current_round' => 1]);
        $this->generateContributions($circle);

        SendCircleStartedNotifications::dispatch($circle);
    }

    public function recordContribution(FundCircle $circle, int $userId, ?string $paymentRef): array
    {
        $member = CircleMember::where('circle_id', $circle->id)
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            return ['success' => false, 'message' => 'أنت لست عضواً في هذه الحلقة'];
        }

        if (in_array($member->status, ['frozen', 'removed'])) {
            return ['success' => false, 'message' => 'لا يمكن المساهمة — حسابك مجمد أو مستبعد'];
        }

        if ($circle->status !== 'active') {
            return ['success' => false, 'message' => 'الحلقة غير نشطة حالياً'];
        }

        return DB::transaction(function () use ($circle, $member, $paymentRef) {
            $contribution = Contribution::where('circle_id', $circle->id)
                ->where('member_id', $member->id)
                ->where('round_number', $circle->current_round)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (!$contribution) {
                return ['success' => false, 'message' => 'لا توجد مساهمة مستحقة حالياً'];
            }

            $contribution->update([
                'status' => 'paid',
                'payment_ref' => $paymentRef,
                'paid_at' => now(),
            ]);

            $member->increment('total_contributed', $contribution->amount);

            // Trust score: on-time payment
            app(TrustScoreService::class)->addPoints($member->user, 'monthly_payment_on_time');

            AuditService::log('contribution_paid', $contribution, null, [
                'circle_id' => $circle->id,
                'amount' => $contribution->amount,
                'round' => $circle->current_round,
            ]);

            $this->checkRoundCompletion($circle);

            return ['success' => true, 'message' => 'تم تسجيل المساهمة بنجاح'];
        });
    }

    public function getDashboardData(FundCircle $circle, int $userId): array
    {
        $member = CircleMember::where('circle_id', $circle->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $circle->load('city:id,name');

        $members = CircleMember::where('circle_id', $circle->id)
            ->with('user:id,name')
            ->orderBy('payout_order')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'name' => $m->user->name,
                'payout_order' => $m->payout_order,
                'has_received_payout' => $m->has_received_payout,
                'total_contributed' => $m->total_contributed,
                'status' => $m->status,
            ]);

        $contributions = Contribution::where('circle_id', $circle->id)
            ->where('member_id', $member->id)
            ->orderBy('round_number')
            ->get();

        $timeline = $members->map(fn($m) => [
            'round' => $m['payout_order'],
            'name' => $m['name'],
            'status' => $m['has_received_payout'] ? 'done'
                : ($m['payout_order'] === $circle->current_round ? 'current' : 'pending'),
        ]);

        return [
            'circle' => $circle,
            'my_membership' => $member,
            'members' => $members,
            'contributions' => $contributions,
            'timeline' => $timeline,
            'total_payout' => $circle->monthly_amount * $circle->max_members,
        ];
    }

    private function generateContributions(FundCircle $circle): void
    {
        $members = CircleMember::where('circle_id', $circle->id)->get();

        foreach ($members as $member) {
            Contribution::create([
                'circle_id' => $circle->id,
                'member_id' => $member->id,
                'amount' => $circle->monthly_amount,
                'round_number' => $circle->current_round,
                'due_date' => now()->addDays(30),
            ]);
        }
    }

    private function checkRoundCompletion(FundCircle $circle): void
    {
        $pendingCount = Contribution::where('circle_id', $circle->id)
            ->where('round_number', $circle->current_round)
            ->where('status', '!=', 'paid')
            ->count();

        if ($pendingCount > 0) {
            return;
        }

        // Issue payout to current round's recipient via background job
        $recipient = CircleMember::where('circle_id', $circle->id)
            ->where('payout_order', $circle->current_round)
            ->first();

        if ($recipient) {
            IssuePayout::dispatch($circle, $recipient);
        }
    }
}
