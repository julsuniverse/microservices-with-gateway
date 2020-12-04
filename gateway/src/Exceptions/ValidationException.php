<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class ValidationException extends \Exception
{
    /**
     * @var array
     */
    private $violations;

    /**
     * ValidationException constructor.
     * @param array $violations
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        array $violations,
        string $message = 'Invalid input.',
        int $code = 422,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->violations = $violations;
    }

    /**
     * @return array
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}