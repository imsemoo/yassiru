<?php

namespace App\Notifications;

use App\Models\FundCircle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CircleStarted extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(public FundCircle $circle) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("بدأت حلقة {$this->circle->name}")
            ->greeting("مرحباً {$notifiable->name}")
            ->line("اكتمل عدد أعضاء حلقة \"{$this->circle->name}\" وبدأت الجولة الأولى.")
            ->line("المبلغ الشهري: {$this->circle->monthly_amount} {$this->circle->currency}")
            ->action('لوحة الحلقة', url("/circles/{$this->circle->id}/dashboard"))
            ->line('يرجى دفع مساهمتك قبل نهاية الشهر.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'circle_started',
            'circle_id' => $this->circle->id,
            'circle_name' => $this->circle->name,
            'message' => "بدأت حلقة {$this->circle->name}",
        ];
    }
}
