<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exceptions\ValidationException;
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
    private $dataBaseUrl;
    /**
     * @var string
     */
    private $authBaseUrl;

    public function __construct(Client $client, string $dataBaseUrl, string $authBaseUrl)
    {
        $this->client = $client;
        $this->dataBaseUrl = $dataBaseUrl;
        $this->authBaseUrl = $authBaseUrl;
    }

    public function request(array $data, string $url, string $method): array
    {
        try {
            $response = $this->client->request($method, $this->dataBaseUrl . $url, [
                'json' => $data
            ]);
            if (in_array($response->getStatusCode(), [200, 201])) {
                return json_decode((string)$response->getBody(), true);
            }
            if ($response->getStatusCode() === 204) {
                return [];
            }
            throw new \DomainException('Something went wrong');
        } catch (ConnectException $e) {
            throw new \DomainException('Connection Exception');
        } catch (GuzzleException $e) {
            $response = json_decode((string)$e->getResponse()->getBody());
            if (isset($response->violations)) {
                throw new ValidationException($response->violations);
            }
            throw new \DomainException($response->error->message ?? $e->getMessage());
        } catch (\Throwable $e) {
            throw new \DomainException($e->getMessage());
        }
    }

    public function token(array $data, string $url, string $method)
    {
        try {
            $response = $this->client->request($method, $this->authBaseUrl . $url, [
                'headers'  => ['content-type' => 'application/x-www-form-urlencoded'],
                'form_params' => array_merge($data, ['client_id' => 'app', 'client_secret' => '']),
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