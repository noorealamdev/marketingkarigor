<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ContentApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task, public User $approvedBy) {}

    public function via(object $notifiable): array
    {
        return collect(['database', 'mail'])
            ->filter(fn ($channel) => $notifiable->wantsNotification(NotificationType::ContentApproved, $channel))
            ->values()->all();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Approved by client: {$this->task->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("**{$this->task->name}** has been approved by {$this->approvedBy->name}.")
            ->when($this->task->project, fn ($m) => $m->line("Project: {$this->task->project->name}"))
            ->action('View Task', url("/tasks/{$this->task->slug}"))
            ->line('Thank you for using ' . config('app.name') . '!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'          => 'content_approved',
            'title'         => "Approved: {$this->task->name}",
            'body'          => "Approved by {$this->approvedBy->name}",
            'url'           => "/tasks/{$this->task->slug}",
            'task_id'       => $this->task->id,
            'task_name'     => $this->task->name,
            'actor_name'    => $this->approvedBy->name,
            'actor_initial' => strtoupper(substr($this->approvedBy->name, 0, 1)),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
