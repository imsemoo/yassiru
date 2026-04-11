<?php

namespace App\Notifications;

use App\Models\Recommendation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRecommendation extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(public Recommendation $recommendation) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('توصية زواج جديدة')
            ->greeting("مرحباً {$notifiable->name}")
            ->line('تم إنشاء توصية زواج جديدة تخص أحد مرشحيك.')
            ->line("نسبة التوافق: {$this->recommendation->compatibility_score}%")
            ->action('عرض التوصية', url('/recommender'))
            ->line('يرجى مراجعة التوصية والتواصل مع الأهل.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_recommendation',
            'recommendation_id' => $this->recommendation->id,
            'score' => $this->recommendation->compatibility_score,
            'message' => 'توصية زواج جديدة',
        ];
    }
}
