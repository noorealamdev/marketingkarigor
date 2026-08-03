<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\PerformanceReport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReportShared extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PerformanceReport $report, public User $sharedBy) {}

    public function via(object $notifiable): array
    {
        return collect(['database', 'mail'])
            ->filter(fn ($channel) => $notifiable->wantsNotification(NotificationType::ReportShared, $channel))
            ->values()->all();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $period = $this->report->periodEnum()->label();
        $range  = $this->report->period_start->format('M d') . ' – ' . $this->report->period_end->format('M d, Y');

        return (new MailMessage)
            ->subject("Your {$period} Performance Report ({$range})")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your {$period} performance report for {$range} is ready to view.")
            ->when($this->report->reach, fn ($m) => $m->line("**Reach:** " . number_format($this->report->reach)))
            ->when($this->report->engagement, fn ($m) => $m->line("**Engagement:** " . number_format($this->report->engagement)))
            ->when($this->report->video_views, fn ($m) => $m->line("**Video Views:** " . number_format($this->report->video_views)))
            ->action('View Report', url("/portal/reports/{$this->report->id}"))
            ->line('Thank you for using ' . config('app.name') . '!');
    }

    public function toDatabase(object $notifiable): array
    {
        $period = $this->report->periodEnum()->label();

        return [
            'type'          => 'report_shared',
            'title'         => "{$period} report shared with you",
            'body'          => $this->report->period_start->format('M d') . ' – ' . $this->report->period_end->format('M d, Y'),
            'url'           => "/portal/reports/{$this->report->id}",
            'report_id'     => $this->report->id,
            'actor_name'    => $this->sharedBy->name,
            'actor_initial' => strtoupper(substr($this->sharedBy->name, 0, 1)),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
