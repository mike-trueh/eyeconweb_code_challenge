<?php

namespace App\Services\Cloudflare;

interface CloudflareApiInterface
{
    public function setToken(string $token): self;

    public function setApiKey(string $email, string $apiKey): self;

    public function validateCredentials(): bool;

    public function getDomains(int $page, int $perPage = 20): array;
}
