<?php

namespace App\Listeners\User;

use App\Events\User\UserCreatedEvent;
use App\Jobs\User\ImportDomainsJob;

class ImportCloudflareDomainsListener
{
    /**
     * Handle the event.
     *
     * @param UserCreatedEvent $event
     * @return void
     */
    public function handle(UserCreatedEvent $event): void
    {
        dispatch(new ImportDomainsJob($event->user));
    }
}
