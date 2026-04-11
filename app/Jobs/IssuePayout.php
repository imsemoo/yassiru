<?php

namespace App\Jobs;

use App\Models\CircleMember;
use App\Models\FundCircle;
use App\Models\Payout;
use App\Notifications\PayoutReceived;
use App\Services\AuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IssuePayout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(
        public FundCircle $circle,
        public CircleMember $recipient,
    ) {}

    public function handle(): void
    {
        // Prevent duplicate payouts
        if (Payout::where('circle_id', $this->circle->id)
            ->where('round_number', $this->circle->current_round)
            ->exists()) {
            return;
        }

        $payout = Payout::create([
            'circle_id' => $this->circle->id,
            'member_id' => $this->recipient->id,
            'amount' => $this->circle->monthly_amount * $this->circle->max_members,
            'round_number' => $this->circle->current_round,
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $this->recipient->update(['has_received_payout' => true]);
        $this->recipient->user->notify(new PayoutReceived($payout, $this->circle));

        AuditService::log('payout_issued', $payout, null, [
            'circle_id' => $this->circle->id,
            'member_id' => $this->recipient->id,
            'amount' => $payout->amount,
            'round' => $this->circle->current_round,
        ]);

        // Advance to next round or complete
        if ($this->circle->current_round >= $this->circle->cycle_months) {
            $this->circle->update(['status' => 'completed']);
        } else {
            $this->circle->increment('current_round');
            $this->generateNextRoundContributions();
        }
    }

    private function generateNextRoundContributions(): void
    {
        $members = CircleMember::where('circle_id', $this->circle->id)
            ->where('status', 'active')
            ->get();

        foreach ($members as $member) {
            \App\Models\Contribution::create([
                'circle_id' => $this->circle->id,
                'member_id' => $member->id,
                'amount' => $this->circle->monthly_amount,
                'round_number' => $this->circle->current_round,
                'due_date' => now()->addDays(30),
            ]);
        }
    }
}
