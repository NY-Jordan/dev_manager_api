<?php

namespace App\Listeners;

use App\Events\UserGuestInProject;
use App\Jobs\SendEmailToUserGuestInProject;
use App\Models\ProjectInvitaion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleUserGuestInProject
{
    /**
     * Create the event listener.
     */
    public function __construct(
        Private ProjectInvitaion $projectInvitaion
    )
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserGuestInProject $event): void
    {
        $invitation = $this->projectInvitaion->newInvitation($event->user->id, $event->project->id);
        dispatch(new SendEmailToUserGuestInProject($event->user, $invitation));
    }
}
