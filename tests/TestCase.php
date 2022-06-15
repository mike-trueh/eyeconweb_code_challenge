<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;

    public function getDomainsResult(int $domains = 0, int $page = 1, int $totalPages = 1): array
    {
        return [
            'result' => $this->generateDomains($domains),
            'result_info' => [
                'page' => $page,
                'total_pages' => $totalPages,
            ]
        ];
    }

    public function generateDomains(int $count): array
    {
        $domains = [];

        for ($i = 0; $i < $count; $i++) {
            $domains[] = [
                'id' => $this->faker->uuid(),
                'external_id' => $this->faker->uuid(),
                'name' => $this->faker->domainName(),
                'status' => 'active',
                'paused' => rand(0, 1),
                'data' => [],
            ];
        }

        return $domains;
    }
}
