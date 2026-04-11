<?php

namespace App\Jobs;

use App\Models\Contribution;
use App\Models\FundCircle;
use App\Services\GuaranteeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckOverdueContributions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function handle(GuaranteeService $guaranteeService): void
    {
        // Find all overdue contributions in active circles
        $overdueContributions = Contribution::where('status', 'pending')
            ->where('due_date', '<', now())
            ->whereHas('circle', fn ($q) => $q->where('status', 'active'))
            ->with(['member', 'circle'])
            ->get();

        foreach ($overdueContributions as $contribution) {
            if (!$contribution->member || !$contribution->circle) {
                continue;
            }

            $guaranteeService->handleLatePayment(
                $contribution->circle,
                $contribution->member,
                $contribution,
            );
        }
    }
}
