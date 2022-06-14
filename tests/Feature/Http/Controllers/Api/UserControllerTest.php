<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Events\User\UserCreatedEvent;
use App\Jobs\User\ImportDomainsJob;
use App\Models\Domain;
use App\Models\User;
use App\Services\Cloudflare\CloudflareApiInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Mockery;
use Queue;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    public function test_user_domains_resource_exists()
    {
        $this->user = User::factory()->create();

        $response = $this->getJson('/api/v1/user/' . $this->user->id . '/domains');
        $response->assertStatus(200);
    }

    public function test_user_domains_resource_returns_valid_data()
    {
        $this->user = User::factory()->create();

        $response = $this->getJson('/api/v1/user/' . $this->user->id . '/domains');
        $this->assertEquals(0, $response->json('total'));

        $domains = Domain::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/v1/user/' . $this->user->id . '/domains');
        $this->assertEquals(count($domains), $response->json('total'));
        $this->assertEquals(Domain::PER_PAGE, $response->json('per_page'));
    }

    public function test_user_domains_resource_returns_only_user_domains()
    {
        $this->user = User::factory()->create();

        $domains = Domain::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $secondUser = User::factory()->create();
        Domain::factory()->count(3)->create([
            'user_id' => $secondUser->id,
        ]);

        $response = $this->getJson('/api/v1/user/' . $this->user->id . '/domains');
        $this->assertEquals($domains->pluck('name'), collect($response->json('data'))->pluck('name'));
    }

    public function test_user_creation_without_cloudflare_credentials()
    {
        Event::fake();

        $request = $this->postJson('/api/v1/user', []);
        $request->assertStatus(422);

        $request = $this->postJson('/api/v1/user', [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
        ]);
        $request->assertStatus(200);

        Event::assertDispatched(UserCreatedEvent::class);
    }

    public function test_user_creation_with_cloudflare_token()
    {
        $mock = Mockery::mock(CloudflareApiInterface::class);
        $mock->shouldReceive('setToken')->times(2);
        $mock->shouldReceive('validateCredentials')->once()->andReturn(false);
        $this->app->instance(CloudflareApiInterface::class, $mock);

        $request = $this->postJson('/api/v1/user', [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'cloudflare_token' => $this->faker->uuid(),
        ]);
        $request->assertStatus(422);

        $mock->shouldReceive('validateCredentials')->once()->andReturn(true);
        $this->app->instance(CloudflareApiInterface::class, $mock);

        Queue::fake();

        $request = $this->postJson('/api/v1/user', [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'cloudflare_token' => $this->faker->uuid(),
        ]);
        $request->assertStatus(200);

        Queue::assertPushed(ImportDomainsJob::class);
    }

    public function test_user_creation_with_cloudflare_api_key()
    {
        $mock = Mockery::mock(CloudflareApiInterface::class);
        $mock->shouldReceive('setApiKey')->times(2);
        $mock->shouldReceive('validateCredentials')->once()->andReturn(false);
        $this->app->instance(CloudflareApiInterface::class, $mock);

        $request = $this->postJson('/api/v1/user', [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'cloudflare_api_key' => $this->faker->uuid(),
        ]);
        $request->assertStatus(422);

        $mock->shouldReceive('validateCredentials')->once()->andReturn(true);
        $this->app->instance(CloudflareApiInterface::class, $mock);

        Queue::fake();

        $request = $this->postJson('/api/v1/user', [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'cloudflare_api_key' => $this->faker->uuid(),
        ]);
        $request->assertStatus(200);

        Queue::assertPushed(ImportDomainsJob::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
