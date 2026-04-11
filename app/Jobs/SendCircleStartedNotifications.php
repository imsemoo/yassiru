<?php

namespace App\Jobs;

use App\Models\CircleMember;
use App\Models\FundCircle;
use App\Notifications\CircleStarted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCircleStartedNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(
        public FundCircle $circle,
    ) {}

    public function handle(): void
    {
        $members = CircleMember::where('circle_id', $this->circle->id)
            ->with('user')
            ->get();

        foreach ($members as $member) {
            if ($member->user) {
                $member->user->notify(new CircleStarted($this->circle));
            }
        }
    }
}
