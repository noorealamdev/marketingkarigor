<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ContentReadyForApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task, public User $sharedBy) {}

    public function via(object $notifiable): array
    {
        return collect(['database', 'mail'])
            ->filter(fn ($channel) => $notifiable->wantsNotification(NotificationType::ContentReadyForApproval, $channel))
            ->values()->all();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New content ready for your approval: {$this->task->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("We've finished **{$this->task->name}** and it's ready for your review.")
            ->when($this->task->project, fn ($m) => $m->line("Project: {$this->task->project->name}"))
            ->action('Review Content', url("/portal/content/{$this->task->slug}"))
            ->line('Thank you for using ' . config('app.name') . '!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'          => 'content_ready_for_approval',
            'title'         => "Ready for approval: {$this->task->name}",
            'body'          => "Shared by {$this->sharedBy->name}",
            'url'           => "/portal/content/{$this->task->slug}",
            'task_id'       => $this->task->id,
            'task_name'     => $this->task->name,
            'actor_name'    => $this->sharedBy->name,
            'actor_initial' => strtoupper(substr($this->sharedBy->name, 0, 1)),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
