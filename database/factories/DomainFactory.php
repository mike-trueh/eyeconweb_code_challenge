<?php

namespace Database\Factories;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Domain>
 */
class DomainFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['external_id' => "string", 'name' => "string", 'status' => "string", 'paused' => "bool", 'data' => "false|string"])]
    public function definition(): array
    {
        return [
            'external_id' => $this->faker->uuid(),
            'name' => $this->faker->domainName(),
            'status' => 'active',
            'paused' => true,
            'data' => json_encode([]),
        ];
    }
}
