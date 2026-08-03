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

class TaskCommented extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task, public TaskComment $comment, public User $commenter) {}

    public function via(object $notifiable): array
    {
        return collect(['database', 'mail'])
            ->filter(fn ($channel) => $notifiable->wantsNotification(NotificationType::TaskCommented, $channel))
            ->values()->all();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("{$this->commenter->name} commented on: {$this->task->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->commenter->name} left a comment on **{$this->task->name}**:")
            ->when($this->comment->body, fn ($m) => $m->line('"' . $this->comment->body . '"'), fn ($m) => $m->line('(Sent an attachment)'))
            ->action('View Task', url("/tasks/{$this->task->slug}"))
            ->line('Thank you for using ' . config('app.name') . '!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'          => 'task_commented',
            'title'         => "{$this->commenter->name} commented on: {$this->task->name}",
            'body'          => $this->comment->body !== '' ? \Str::limit($this->comment->body, 100) : 'Sent an attachment',
            'url'           => "/tasks/{$this->task->slug}",
            'task_id'       => $this->task->id,
            'task_name'     => $this->task->name,
            'actor_name'    => $this->commenter->name,
            'actor_initial' => strtoupper(substr($this->commenter->name, 0, 1)),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
