<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Register;

use App\Model\User\Entity\Id;
use App\Model\User\Entity\User;
use App\Model\User\Entity\UserRepository;
use App\Model\User\Service\PasswordGenerator;
use App\Model\User\Service\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var PasswordHasher
     */
    private $passwordsHasher;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        PasswordHasher $passwordsHasher,
        UserRepository $userRepository
    ) {
        $this->passwordsHasher = $passwordsHasher;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    public function handle(Command $command)
    {
        if ($this->userRepository->hasByEmail($command->email)) {
            throw new \DomainException('User with such email already exists.');
        }

        $user = new User(
            Id::next(),
            $command->email,
            $this->passwordsHasher->hash($command->password),
            new \DateTimeImmutable()
        );

        $this->em->persist($user);
        $this->em->flush();
    }
}