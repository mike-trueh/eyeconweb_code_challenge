<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_domains_index_resource_exists()
    {
        $response = $this->get('api/v1/domain');
        $response->assertStatus(200);
    }

    public function test_domains_index_resource_returns_valid_data()
    {
        $response = $this->get('api/v1/domain');
        $this->assertEquals(0, $response->json('total'));

        $user = User::factory()->create();

        $domains = Domain::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->get('api/v1/domain');
        $this->assertEquals(count($domains), $response->json('total'));
        $this->assertEquals(Domain::PER_PAGE, $response->json('per_page'));
    }
}
