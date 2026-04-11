<?php

namespace App\Notifications;

use App\Models\Contribution;
use App\Models\FundCircle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributionReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(
        public Contribution $contribution,
        public FundCircle $circle,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = now()->diffInDays($this->contribution->due_date, false);

        return (new MailMessage)
            ->subject("تذكير: مساهمتك مستحقة خلال {$daysLeft} أيام")
            ->greeting("مرحباً {$notifiable->name}")
            ->line("تذكير بموعد مساهمتك الشهرية في حلقة \"{$this->circle->name}\".")
            ->line("المبلغ: {$this->contribution->amount} {$this->circle->currency}")
            ->line("الموعد النهائي: {$this->contribution->due_date->format('Y-m-d')}")
            ->action('دفع المساهمة', url("/circles/{$this->circle->id}/dashboard"))
            ->line('يرجى الدفع قبل الموعد لتجنب أي تأخير.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'contribution_reminder',
            'circle_id' => $this->circle->id,
            'circle_name' => $this->circle->name,
            'amount' => $this->contribution->amount,
            'due_date' => $this->contribution->due_date->toISOString(),
            'message' => "تذكير: مساهمتك في حلقة {$this->circle->name} مستحقة قريباً",
        ];
    }
}
