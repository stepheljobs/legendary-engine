<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Invitation $invitation
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $tenant = $this->invitation->tenant;
        $inviter = $this->invitation->inviter;
        $acceptUrl = route('invitations.show', $this->invitation->token);

        return (new MailMessage)
            ->subject("You've been invited to join {$tenant->company_name}")
            ->greeting("Hello!")
            ->line($inviter ? "{$inviter->name} has invited you to join {$tenant->company_name}." : "You've been invited to join {$tenant->company_name}.")
            ->line("You'll be joining as a {$this->invitation->role_name}.")
            ->action('Accept Invitation', $acceptUrl)
            ->line('This invitation will expire on ' . $this->invitation->expires_at->format('F j, Y'))
            ->line('If you did not expect this invitation, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'tenant_id' => $this->invitation->tenant_id,
            'role_name' => $this->invitation->role_name,
        ];
    }
}
