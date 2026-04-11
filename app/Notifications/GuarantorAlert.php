<?php

namespace App\Notifications;

use App\Models\FundCircle;
use App\Models\Guarantor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GuarantorAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(
        public FundCircle $circle,
        public string $memberName,
        public float $amount,
        public string $currency,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'guarantor_alert',
            'circle_id' => $this->circle->id,
            'circle_name' => $this->circle->name,
            'member_name' => $this->memberName,
            'amount' => $this->amount,
            'message' => "تنبيه: الشخص المكفول ({$this->memberName}) متأخر عن سداد {$this->amount} {$this->currency} في حلقة {$this->circle->name}",
        ];
    }
}
