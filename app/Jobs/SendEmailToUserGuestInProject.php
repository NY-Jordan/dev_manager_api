<?php

namespace App\Jobs;

use App\Models\ProjectInvitaion;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailToUserGuestInProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private User $user,
        private ProjectInvitaion $invitation
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        dd('ici');
        Mail::send('emails.email_to_guest_in_project',['user' => $this->user, 'invitation' => $this->invitation], function($message) {
            $message->to($this->user->email, "DevHandle")
                    ->from('devhandle@contact.net')
                    ->subject("Email Verification");    
        });
    }
}