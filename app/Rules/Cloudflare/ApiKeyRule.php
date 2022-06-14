<?php

namespace App\Rules\Cloudflare;

use App\Services\Cloudflare\CloudflareApiInterface;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class ApiKeyRule implements Rule, DataAwareRule
{
    /**
     * All the data under validation.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Set the data under validation.
     *
     * @param array $data
     * @return $this
     */
    public function setData($data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $api = resolve(CloudflareApiInterface::class);

        return $api->setApiKey($this->data['email'], $value)->validateCredentials();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'API Key not valid';
    }
}
