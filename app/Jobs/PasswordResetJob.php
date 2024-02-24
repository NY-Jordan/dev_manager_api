<?php

namespace App\Jobs;

use App\Enums\OtpEnums;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class PasswordResetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user
        )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $otp = (new Otp())->createOtp($this->user->id, OtpEnums::RESET_PASSWORD);
        Mail::send('emails.reset_password',['otp' => $otp, 'user' => $this->user], function($message) {
            $message->to($this->user->email, "DevHandle")
                    ->from('devhandle@contact.net')
                    ->subject("Password Reset");    
        });
    }
}
