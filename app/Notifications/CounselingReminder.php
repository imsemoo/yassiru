<?php

namespace App\Notifications;

use App\Models\CounselingSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CounselingReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(public CounselingSession $session) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $type = $this->session->type === 'individual' ? 'فردية' : 'جماعية';

        return (new MailMessage)
            ->subject('تذكير بموعد جلستك الاستشارية')
            ->greeting("مرحباً {$notifiable->name}")
            ->line("تذكير: لديك جلسة استشارية {$type} غداً.")
            ->line("الموعد: {$this->session->scheduled_at->format('Y-m-d H:i')}")
            ->action('جلساتي', url('/counseling'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'counseling_reminder',
            'session_id' => $this->session->id,
            'scheduled_at' => $this->session->scheduled_at->toISOString(),
            'message' => 'تذكير بموعد جلستك الاستشارية غداً',
        ];
    }
}
