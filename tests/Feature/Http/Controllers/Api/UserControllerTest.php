<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    public function test_user_domains_resource_exists()
    {
        $this->user = User::factory()->create();

        $response = $this->get('/api/v1/user/' . $this->user->id . '/domains');
        $response->assertStatus(200);
    }

    public function test_user_domains_resource_returns_valid_data()
    {
        $this->user = User::factory()->create();

        $response = $this->get('/api/v1/user/' . $this->user->id . '/domains');
        $this->assertEquals(0, $response->json('total'));

        $domains = Domain::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get('/api/v1/user/' . $this->user->id . '/domains');
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

        $response = $this->get('/api/v1/user/' . $this->user->id . '/domains');
        $this->assertEquals($domains->pluck('name'), collect($response->json('data'))->pluck('name'));
    }

    protected function setUp(): void
    {
        parent::setUp();
    }
}
