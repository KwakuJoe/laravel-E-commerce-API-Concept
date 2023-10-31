<?php

namespace App\Listeners;

use App\Events\SendEmailVerificationEvent;
use App\Notifications\AccountCreatedSuccessfullNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;


class sendEmailVerificationListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    // use Queueable;

      /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'email-verification-listeners';

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 60;

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendEmailVerificationEvent $event): void
    {
        $user = $event->user;
        $url  = $event->url;

        // $delay = now()->addMinutes(1);

        // send notification
        // $user->notify((new AccountCreatedSuccessfullNotification($user, $url))->delay($delay));
        $user->notify((new AccountCreatedSuccessfullNotification($user, $url)));


    }
}
