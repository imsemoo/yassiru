<?php

namespace App\Notifications;

use App\Models\FundCircle;
use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(public Payout $payout, public FundCircle $circle) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تم صرف حصتك من الصندوق!')
            ->greeting("مبارك يا {$notifiable->name}")
            ->line("تم صرف حصتك من حلقة \"{$this->circle->name}\".")
            ->line("المبلغ: {$this->payout->amount} {$this->circle->currency}")
            ->action('تفاصيل الحلقة', url("/circles/{$this->circle->id}/dashboard"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payout_received',
            'circle_id' => $this->circle->id,
            'amount' => $this->payout->amount,
            'message' => "تم صرف {$this->payout->amount} {$this->circle->currency} من حلقة {$this->circle->name}",
        ];
    }
}
