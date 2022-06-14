<?php

namespace Tests\Feature\Console\Commands\User;

use App\Jobs\User\ImportDomainsJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Queue;
use Tests\TestCase;

class EveryDayImportCommandTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_command_is_running_without_users()
    {
        $this->artisan('cloudflare:import')
            ->expectsOutput('Import job dispatched for 0 user(s)')
            ->assertSuccessful();

        User::factory()->count(3)->create();

        $this->artisan('cloudflare:import')
            ->expectsOutput('Import job dispatched for 0 user(s)')
            ->assertSuccessful();
    }

    public function test_command_is_running_with_users()
    {
        $users = User::factory()->count(3)->create([
            'cloudflare_token' => $this->faker->uuid(),
        ]);

        Queue::fake();
        $this->artisan('cloudflare:import')
            ->expectsOutput('Import job dispatched for ' . $users->count() . ' user(s)')
            ->assertSuccessful();
        Queue::assertPushed(ImportDomainsJob::class);

        $this->artisan('cloudflare:import --user=' . $users->first()->id)
            ->expectsOutput('Import job dispatched for 1 user(s)')
            ->assertSuccessful();
        Queue::assertPushed(ImportDomainsJob::class);
    }
}
