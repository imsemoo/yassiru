<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $desc = match ($this->payment->type->value) {
            'contribution' => 'مساهمة الصندوق',
            'guarantee_fee' => 'رسوم الضمان',
            'wedding' => 'تسجيل العرس',
            default => 'عملية دفع',
        };

        return (new MailMessage)
            ->subject("تم تأكيد الدفع — {$desc}")
            ->greeting("مرحباً {$notifiable->name}")
            ->line("تم تأكيد {$desc} بنجاح.")
            ->line("المبلغ: {$this->payment->amount} {$this->payment->currency}")
            ->line("رقم المرجع: {$this->payment->merchant_ref}")
            ->action('عرض التفاصيل', url('/'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_confirmed',
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'payment_type' => $this->payment->type->value,
            'message' => "تم تأكيد دفع {$this->payment->amount} {$this->payment->currency}",
        ];
    }
}
