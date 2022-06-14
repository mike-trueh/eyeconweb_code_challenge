<?php

namespace App\Listeners\User;

use App\Events\User\UserCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportCloudflareDomainsListener implements ShouldQueue
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
     * @param UserCreatedEvent $event
     * @return void
     */
    public function handle(UserCreatedEvent $event)
    {
        //
    }
}
