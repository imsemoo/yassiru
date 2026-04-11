<?php

use App\Jobs\CheckOverdueContributions;
use App\Jobs\ProcessContributionReminders;
use App\Models\CounselingSession;
use App\Notifications\CounselingReminder;
use Illuminate\Support\Facades\Schedule;

// Every day at 9am Cairo time: send reminders for contributions due in 3 days
Schedule::job(new ProcessContributionReminders)
    ->dailyAt('09:00')
    ->timezone('Africa/Cairo')
    ->withoutOverlapping();

// Every day at 10am: check overdue contributions and run escalation
Schedule::job(new CheckOverdueContributions)
    ->dailyAt('10:00')
    ->timezone('Africa/Cairo')
    ->withoutOverlapping();

// Every day at 11am: send counseling session reminders for tomorrow
Schedule::call(function () {
    $sessions = CounselingSession::where('scheduled_at', '>=', now()->addHours(20))
        ->where('scheduled_at', '<=', now()->addHours(28))
        ->where('status', 'scheduled')
        ->with('user')
        ->get();

    foreach ($sessions as $session) {
        $session->user->notify(new CounselingReminder($session));
    }
})->dailyAt('11:00')
  ->timezone('Africa/Cairo')
  ->name('counseling-reminders')
  ->withoutOverlapping();
