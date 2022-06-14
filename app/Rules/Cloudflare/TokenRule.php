<?php

namespace App\Rules\Cloudflare;

use App\Services\Cloudflare\CloudflareApiInterface;
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
        $api = app(CloudflareApiInterface::class);

        return $api->setToken($value)->validateCredentials();
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
