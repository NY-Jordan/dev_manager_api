<?php

namespace App\Notifications;

use App\Models\Notification;
use App\Models\ProjectInvitaion;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectInvitationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private User $user )
    {
        //
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['broadcast'];
    }

    /**
     * Get the type of the notification being broadcast.
     */
    public function broadcastType(): string
    {
        return 'notification.invitaion';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->user->id),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(): BroadcastMessage
    {
/*         Notification::createNotification(Notification);
 */        return new BroadcastMessage([
            'invoice_id' => 'cf',
            'amount' => "dd",
        ]);
    }
}
