<?php

namespace App\Notifications;

use App\Models\GroupWedding;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeddingRegistrationConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(public GroupWedding $wedding) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تم تسجيلك في العرس الجماعي')
            ->greeting("مرحباً {$notifiable->name}")
            ->line("تم تسجيلك بنجاح في \"{$this->wedding->title}\".")
            ->line("المكان: {$this->wedding->venue_name}")
            ->line("التاريخ: {$this->wedding->wedding_date->format('Y-m-d')}")
            ->line("التكلفة: {$this->wedding->price_per_groom}")
            ->action('تسجيلاتي', url('/my-weddings'))
            ->line('يرجى إتمام الدفع قبل الموعد المحدد.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'wedding_registered',
            'wedding_id' => $this->wedding->id,
            'title' => $this->wedding->title,
            'message' => "تم تسجيلك في {$this->wedding->title}",
        ];
    }
}
