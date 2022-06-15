<?php

namespace App\Services\Cloudflare;

use App\Services\Cloudflare\Exceptions\CloudflareException;
use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Adapter\ResponseException;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Auth\Auth;
use Cloudflare\API\Endpoints\Zones;
use stdClass;

class CloudflareGuzzleSDK implements CloudflareApiInterface
{
    /**
     * @var Auth|null
     */
    protected ?Auth $auth = null;

    /**
     * @var Adapter|null
     */
    protected ?Adapter $adapter;

    /**
     * @param string $token
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->auth = new APIToken($token);

        return $this;
    }

    /**
     * @param string $email
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey(string $email, string $apiKey): self
    {
        $this->auth = new APIKey($email, $apiKey);

        return $this;
    }

    /**
     * @throws CloudflareException
     */
    public function validateCredentials(): bool
    {
        $this->setAdapter();

        try {
            $this->performValidateRequest();
            return true;
        } catch (ResponseException $exception) {
            return false;
        }
    }

    /**
     * @throws CloudflareException
     */
    protected function setAdapter()
    {
        if (is_null($this->auth)) {
            throw new CloudflareException('Auth data is empty');
        }

        $this->adapter = $this->getClient();
    }

    /**
     * @return Guzzle
     */
    protected function getClient(): Guzzle
    {
        return new Guzzle($this->auth);
    }

    /**
     * @return void
     */
    public function performValidateRequest(): void
    {
        $this->adapter->get('user/tokens/verify');
    }

    /**
     * @throws CloudflareException
     */
    public function getDomains(int $page = 1, int $perPage = 20, string $name = '', string $status = '', string $order = '', string $direction = '', string $match = 'all'): array
    {
        $this->setAdapter();

        $zones = $this->performZonesRequest($name, $status, $page, $perPage, $order, $direction, $match);

        return $this->convertZonesToDomains($zones);
    }

    /**
     * @param string $name
     * @param string $status
     * @param int $page
     * @param int $perPage
     * @param string $order
     * @param string $direction
     * @param string $match
     * @return stdClass
     */
    protected function performZonesRequest(string $name = '', string $status = '', int $page = 1, int $perPage = 20, string $order = '', string $direction = '', string $match = 'all'): stdClass
    {
        return (new Zones($this->adapter))->listZones($name, $status, $page, $perPage, $order, $direction, $match);
    }

    /**
     * @param object $zones
     * @return array
     */
    protected function convertZonesToDomains(object $zones): array
    {
        $zonesArray = json_decode(json_encode($zones), true);

        return [
            'result' => array_map(function ($domain) {
                $domain['data'] = $domain;
                $domain['external_id'] = $domain['id'];

                return $domain;
            }, $zonesArray['result']),
            'result_info' => $zonesArray['result_info'],
        ];
    }
}
