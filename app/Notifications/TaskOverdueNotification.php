<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Task $task) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Task Overdue: ' . $this->task->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The following task is overdue:')
            ->line('**' . $this->task->title . '**')
            ->line('Project: ' . $this->task->project->name)
            ->line('Due Date: ' . $this->task->due_date->toFormattedDateString())
            ->line('Priority: ' . $this->task->priority->value)
            ->action('View Task', config('app.url'))
            ->line('Please update the task status or extend the due date.');
    }
}
