<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecommenderApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(public bool $approved) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->approved) {
            return (new MailMessage)
                ->subject('تم اعتماد حسابك كمعرّف')
                ->greeting("مرحباً {$notifiable->name}")
                ->line('تم اعتماد حسابك كمعرّف في منصة يسّرو. يمكنك الآن إضافة مرشحين وإنشاء توصيات.')
                ->action('لوحة المعرّف', url('/recommender'));
        }

        return (new MailMessage)
            ->subject('تحديث حالة طلب المعرّف')
            ->greeting("مرحباً {$notifiable->name}")
            ->line('نأسف، لم يتم اعتماد طلبك كمعرّف حالياً. يمكنك التواصل مع الإدارة لمزيد من التفاصيل.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'recommender_status',
            'approved' => $this->approved,
            'message' => $this->approved ? 'تم اعتماد حسابك كمعرّف' : 'لم يتم اعتماد طلب المعرّف',
        ];
    }
}
