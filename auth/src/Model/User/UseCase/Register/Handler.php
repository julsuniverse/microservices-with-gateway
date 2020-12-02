<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Register;

use App\Model\User\Entity\Id;
use App\Model\User\Entity\User;
use App\Model\User\Entity\UserRepository;
use App\Model\User\Service\PasswordGenerator;
use App\Model\User\Service\PasswordHasher;
use App\Repository\AuthRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var UserRepository
     */
    private $authRepository;

    public function __construct(
        AuthRepository $authRepository
    ) {
        $this->authRepository = $authRepository;
    }

    public function handle(Command $command)
    {
        $this->authRepository->register($command);
    }
}