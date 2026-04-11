<?php

namespace App\Notifications;

use App\Models\Contribution;
use App\Models\FundCircle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributionOverdue extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(
        public Contribution $contribution,
        public FundCircle $circle,
        public string $severity = 'warning', // warning, frozen, removed
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLate = now()->diffInDays($this->contribution->due_date);

        $mail = (new MailMessage)
            ->greeting("مرحباً {$notifiable->name}");

        return match ($this->severity) {
            'warning' => $mail
                ->subject('تحذير: مساهمتك متأخرة')
                ->line("مساهمتك في حلقة \"{$this->circle->name}\" متأخرة بـ {$daysLate} أيام.")
                ->line("المبلغ المستحق: {$this->contribution->amount} {$this->circle->currency}")
                ->line('يرجى الدفع فوراً لتجنب تجميد حسابك.')
                ->action('دفع الآن', url("/circles/{$this->circle->id}/dashboard")),

            'frozen' => $mail
                ->subject('تم تجميد حسابك في الحلقة')
                ->line("تم تجميد حسابك في حلقة \"{$this->circle->name}\" بسبب تأخر الدفع ({$daysLate} يوم).")
                ->line('يرجى سداد المبلغ المستحق خلال المهلة لتجنب الاستبعاد النهائي.')
                ->action('دفع الآن', url("/circles/{$this->circle->id}/dashboard")),

            'removed' => $mail
                ->subject('تم استبعادك من الحلقة')
                ->line("تم استبعادك نهائياً من حلقة \"{$this->circle->name}\" بسبب عدم السداد.")
                ->line('تم تغطية المبلغ المستحق من صندوق الضمان.'),
        };
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'contribution_overdue',
            'severity' => $this->severity,
            'circle_id' => $this->circle->id,
            'circle_name' => $this->circle->name,
            'amount' => $this->contribution->amount,
            'message' => match ($this->severity) {
                'warning' => "تحذير: مساهمتك في حلقة {$this->circle->name} متأخرة",
                'frozen' => "تم تجميد حسابك في حلقة {$this->circle->name}",
                'removed' => "تم استبعادك من حلقة {$this->circle->name}",
            },
        ];
    }
}
