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
        $label  = $this->report->title ?: ($this->report->report_type ? "{$this->report->report_type} report" : "{$period} report");

        $mail = (new MailMessage)
            ->subject("Your report is ready — {$range}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your {$label} for {$range} is ready to view.");

        foreach (array_slice($this->report->metrics ?? [], 0, 5) as $metric) {
            if (($metric['value'] ?? '') !== '') {
                $mail->line("**{$metric['label']}:** {$metric['value']}");
            }
        }

        return $mail
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
