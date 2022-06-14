<?php

namespace App\Http\Requests;

use App\Rules\Cloudflare\ApiKeyRule;
use App\Rules\Cloudflare\TokenRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|min:0|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cloudflare_api_key' => [
                'nullable',
                'string',
                new ApiKeyRule,
            ],
            'cloudflare_token' => [
                'nullable',
                'string',
                new TokenRule,
            ],
        ];
    }
}
