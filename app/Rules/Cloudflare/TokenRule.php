<?php

namespace App\Rules\Cloudflare;

use App\Services\Cloudflare\Client\GuzzleClient;
use App\Services\Cloudflare\CloudflareApi;
use Illuminate\Contracts\Validation\Rule;

class TokenRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $client = (new GuzzleClient())->setToken($value);

        return (new CloudflareApi($client))->validateCredentials();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Token not valid';
    }
}
