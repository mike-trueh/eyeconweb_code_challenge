<?php

namespace App\Jobs\User;

use App\Models\User;
use App\Services\Cloudflare\CloudflareApiInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportDomainsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public CloudflareApiInterface $api;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public User $user, public int $page = 1)
    {
        $this->api = resolve(CloudflareApiInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle(): bool
    {
        if (!$this->user->has_credentials) {
            return false;
        }

        $service = $this->user->cloudflare_token ?
            $this->api->setToken($this->user->cloudflare_token) :
            $this->api->setApiKey($this->user->email, $this->user->cloudflare_api_key);

        $domains = $service->getDomains(page: $this->page);

        foreach ($domains['result'] as $domain) {
            $this->user->domains()->updateOrCreate([
                'external_id' => $domain['external_id'],
            ], $domain);
        }

        if ($domains['result_info']['page'] < $domains['result_info']['total_pages']) {
            ImportDomainsJob::dispatch($this->user, ++$this->page);
        }

        return true;
    }
}
