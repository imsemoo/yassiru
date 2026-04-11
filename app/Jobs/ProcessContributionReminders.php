<?php

namespace App\Jobs;

use App\Models\Contribution;
use App\Notifications\ContributionReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessContributionReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function handle(): void
    {
        // Find pending contributions due within 3 days
        $contributions = Contribution::where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(3))
            ->with(['member.user', 'circle'])
            ->get();

        foreach ($contributions as $contribution) {
            if ($contribution->member?->user) {
                $contribution->member->user->notify(
                    new ContributionReminder($contribution, $contribution->circle)
                );
            }
        }
    }
}
