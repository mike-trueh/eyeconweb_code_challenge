<?php

namespace Tests\Feature\Jobs\User;

use App\Jobs\User\ImportDomainsJob;
use App\Models\User;
use App\Services\Cloudflare\CloudflareApiInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class ImportDomainsJobTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_job_not_working_without_credentials()
    {
        $user = User::factory()->create();
        $result = (new ImportDomainsJob($user))->handle();
        $this->assertFalse($result);
    }

    public function test_job_working_with_empty_result()
    {
        $user = User::factory()->create([
            'cloudflare_token' => $this->faker->uuid(),
        ]);

        $mock = Mockery::mock(CloudflareApiInterface::class);
        $mock->shouldReceive('setToken')->once()->andReturnSelf();
        $mock->shouldReceive('getDomains')->with(1)->once()->andReturn($this->getDomainsResult());
        $this->app->instance(CloudflareApiInterface::class, $mock);

        $result = (new ImportDomainsJob($user))->handle();
        $this->assertTrue($result);
    }

    public function test_job_with_domains_in_one_page()
    {
        $user = User::factory()->create([
            'cloudflare_api_key' => $this->faker->uuid(),
        ]);

        $mock = Mockery::mock(CloudflareApiInterface::class);
        $mock->shouldReceive('setApiKey')->once()->andReturnSelf();
        $mock->shouldReceive('getDomains')->with(1)->once()->andReturn($this->getDomainsResult(3));
        $this->app->instance(CloudflareApiInterface::class, $mock);

        $result = (new ImportDomainsJob($user))->handle();
        $this->assertTrue($result);

        $this->assertSame(3, $user->domains()->count());
    }

    public function test_job_recursive_call_with_domains_in_multi_pages()
    {
        $user = User::factory()->create([
            'cloudflare_token' => $this->faker->uuid(),
        ]);

        $mock = Mockery::mock(CloudflareApiInterface::class);
        $mock->shouldReceive('setToken')->once()->andReturnSelf();
        $mock->shouldReceive('getDomains')->with(1)->once()->andReturn($this->getDomainsResult(3, 1, 2));
        $this->app->instance(CloudflareApiInterface::class, $mock);

        Queue::fake();
        $result = (new ImportDomainsJob($user))->handle();
        Queue::assertPushed(ImportDomainsJob::class);
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
