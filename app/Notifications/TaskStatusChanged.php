<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $oldStatus,
        public string $newStatus,
        public User $changedBy
    ) {}

    public function via(object $notifiable): array
    {
        return collect(['database', 'mail'])
            ->filter(fn ($channel) => $notifiable->wantsNotification(NotificationType::TaskStatusChanged, $channel))
            ->values()->all();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $from    = ucwords(str_replace('_', ' ', $this->oldStatus));
        $to      = ucwords(str_replace('_', ' ', $this->newStatus));
        $project = $this->task->project?->name ?? 'No Project';

        return (new MailMessage)
            ->subject("[{$project}] Task status changed: {$this->task->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->changedBy->name} updated the status of a task you're following.")
            ->line("**Task:** {$this->task->name}")
            ->line("**Project:** {$project}")
            ->line("**Status:** {$from} → {$to}")
            ->action('View Task', url("/tasks/{$this->task->slug}"))
            ->line('You are receiving this as an admin, project manager, or task assignee on ' . config('app.name') . '.');
    }

    public function toDatabase(object $notifiable): array
    {
        $from = ucwords(str_replace('_', ' ', $this->oldStatus));
        $to   = ucwords(str_replace('_', ' ', $this->newStatus));

        return [
            'type'          => 'task_status_changed',
            'title'         => "Status updated: {$this->task->name}",
            'body'          => "{$this->changedBy->name} changed status from {$from} → {$to}",
            'url'           => "/tasks/{$this->task->slug}",
            'task_id'       => $this->task->id,
            'task_name'     => $this->task->name,
            'actor_name'    => $this->changedBy->name,
            'actor_initial' => strtoupper(substr($this->changedBy->name, 0, 1)),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
