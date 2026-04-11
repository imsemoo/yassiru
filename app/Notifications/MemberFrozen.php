<?php

namespace App\Notifications;

use App\Models\CircleMember;
use App\Models\FundCircle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberFrozen extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(
        public FundCircle $circle,
        public string $memberName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("تحديث حالة حلقة {$this->circle->name}")
            ->greeting("مرحباً {$notifiable->name}")
            ->line("تم تجميد عضوية أحد أعضاء حلقة \"{$this->circle->name}\" بسبب تأخر السداد.")
            ->line('لا تقلق — صندوق الضمان يغطي أي تعثر لضمان استمرار الحلقة.')
            ->action('لوحة الحلقة', url("/circles/{$this->circle->id}/dashboard"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'member_frozen',
            'circle_id' => $this->circle->id,
            'circle_name' => $this->circle->name,
            'message' => "تم تجميد عضو في حلقة {$this->circle->name} بسبب تأخر السداد",
        ];
    }
}
