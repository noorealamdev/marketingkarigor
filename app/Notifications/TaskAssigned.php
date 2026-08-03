<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task, public User $assignedBy) {}

    public function via(object $notifiable): array
    {
        return collect(['database', 'mail'])
            ->filter(fn ($channel) => $notifiable->wantsNotification(NotificationType::TaskAssigned, $channel))
            ->values()->all();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("You've been assigned: {$this->task->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("You have been assigned to the task **{$this->task->name}** by {$this->assignedBy->name}.")
            ->when($this->task->project, fn($m) => $m->line("Project: {$this->task->project->name}"))
            ->when($this->task->due_date, fn($m) => $m->line("Due: {$this->task->due_date->format('M d, Y')}"))
            ->action('View Task', url("/tasks/{$this->task->slug}"))
            ->line('Thank you for using ' . config('app.name') . '!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'        => 'task_assigned',
            'title'       => "Task assigned: {$this->task->name}",
            'body'        => "Assigned by {$this->assignedBy->name}" . ($this->task->project ? " on {$this->task->project->name}" : ''),
            'url'         => "/tasks/{$this->task->slug}",
            'task_id'     => $this->task->id,
            'task_name'   => $this->task->name,
            'actor_name'  => $this->assignedBy->name,
            'actor_initial' => strtoupper(substr($this->assignedBy->name, 0, 1)),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
