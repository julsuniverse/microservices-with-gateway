<?php

declare(strict_types=1);

namespace App\Repository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;

class UserRepository
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(Client $client, string $baseUrl)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
    }

    public function request(array $data, string $url, string $method)
    {
        try {
            $response = $this->client->request($method, $this->baseUrl . $url, [
                'json' => $data
            ]);
            if (in_array($response->getStatusCode(), [200, 201])) {
                return json_decode((string)$response->getBody(), true);
            }
            throw new \DomainException('Something went wrong');
        } catch (ConnectException $e) {
            throw new \DomainException('Connection Exception');
        } catch (GuzzleException $e) {
            $response = json_decode((string)$e->getResponse()->getBody());
            throw new \DomainException($response->title ?? $e->getMessage());
        } catch (\Throwable $e) {
            throw new \DomainException($e->getMessage());
        }
    }
}