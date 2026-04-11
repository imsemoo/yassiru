<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateEarned extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(public Certificate $certificate) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تهانينا! حصلت على شهادة الجاهزية الزواجية')
            ->greeting("مبارك يا {$notifiable->name}")
            ->line('أتممت بنجاح الدورة التأهيلية بمساراتها الأربعة وحصلت على شهادة الجاهزية الزواجية.')
            ->line("رقم الشهادة: {$this->certificate->certificate_number}")
            ->action('عرض الشهادة', url('/certificate'))
            ->line('يمكنك الآن الاستفادة من جميع خدمات منصة يسّرو.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'certificate_earned',
            'certificate_number' => $this->certificate->certificate_number,
            'message' => 'حصلت على شهادة الجاهزية الزواجية',
        ];
    }
}
