<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RevisionRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task, public TaskComment $comment, public User $requestedBy) {}

    public function via(object $notifiable): array
    {
        return collect(['database', 'mail'])
            ->filter(fn ($channel) => $notifiable->wantsNotification(NotificationType::RevisionRequested, $channel))
            ->values()->all();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Revision requested: {$this->task->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->requestedBy->name} requested a revision on **{$this->task->name}**.")
            ->when($this->comment->body, fn ($m) => $m->line('"' . $this->comment->body . '"'))
            ->action('View Task', url("/tasks/{$this->task->slug}"))
            ->line('Thank you for using ' . config('app.name') . '!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'          => 'revision_requested',
            'title'         => "Revision requested: {$this->task->name}",
            'body'          => $this->comment->body ?: "Requested by {$this->requestedBy->name}",
            'url'           => "/tasks/{$this->task->slug}",
            'task_id'       => $this->task->id,
            'task_name'     => $this->task->name,
            'actor_name'    => $this->requestedBy->name,
            'actor_initial' => strtoupper(substr($this->requestedBy->name, 0, 1)),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
