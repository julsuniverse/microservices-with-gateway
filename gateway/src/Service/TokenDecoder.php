<?php

declare(strict_types=1);

namespace App\Service;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TokenDecoder
{
    /**
     * @var string
     */
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function decode(string $token): object
    {
        $secret = file_get_contents($this->filePath);
        if (!$secret) {
            throw new \RuntimeException('Cannot read token.');
        }

        try {
            $decoded = JWT::decode($token, $secret, array('RS256'));
        } catch (ExpiredException $e) {
            throw new UnauthorizedHttpException('Token expired');
        } catch (\Throwable $e) {
            throw new \RuntimeException('Access denied', 403);
        }

        return $decoded;
    }
}