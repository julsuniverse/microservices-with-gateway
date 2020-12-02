<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User\Entity\User;
use App\Model\User\UseCase\Register\Command;
use App\ReadModel\User\AuthDTO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;

class AuthRepository
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

    public function findByEmail(string $email): AuthDTO
    {
        try {
            $url = $this->baseUrl . '/user/' . $email . '/find';
            $response = $this->client->request('GET', $url);
            if (in_array($response->getStatusCode(), [200, 201])) {
                $response = json_decode((string)$response->getBody(), true);
                return new AuthDTO(
                    $response['id'],
                    $response['email'],
                    $response['password_hash'],
                    $response['role']
                );
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

    public function register(Command $command): array
    {
        try {
            $url = $this->baseUrl . '/register';
            $response = $this->client->request('POST', $url, [
                'json' => [
                    'email' => $command->email,
                    'password' => $command->password
                ]
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