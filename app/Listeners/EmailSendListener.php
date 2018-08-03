<?php

namespace App\Listeners;

use App\Events\RegisteredEvent;
use App\User;
use App\Mail\EmailVerification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailSendListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $data = $event->register_data;
        if ($event->job_type == RegisteredEvent::JOB_TYPE_1) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verify_token' => base64_encode($data['email']),
            ]);
            $email = new EmailVerification($user);
            Mail::to($user->email)->send($email);
        }
    }
}
