<?php

namespace App\Console\Commands\User;

use App\Jobs\User\ImportDomainsJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class EveryDayImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudflare:import {--user=* : User Id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run domain import for users';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $users = User::query()->hasCredentials()->when($userIds = $this->option('user'), function (Builder $query) use ($userIds) {
            return $query->whereIn('id', $userIds);
        })->get();

        foreach ($users as $user) {
            dispatch(new ImportDomainsJob($user));
        }

        $this->info('Import job dispatched for ' . $users->count() . ' user(s)');
    }
}
