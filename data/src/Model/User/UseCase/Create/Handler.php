<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\User\Entity\Id;
use App\Model\User\Entity\User;
use App\Model\User\Entity\UserRepository;
use App\Model\User\Service\PasswordGenerator;
use App\Model\User\Service\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var PasswordGenerator
     */
    private $passwordGenerator;
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
        PasswordGenerator $passwordGenerator,
        PasswordHasher $passwordsHasher,
        UserRepository $userRepository
    ) {
        $this->passwordGenerator = $passwordGenerator;
        $this->passwordsHasher = $passwordsHasher;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    public function handle(Command $command): User
    {
        if ($this->userRepository->hasByEmail($command->email)) {
            throw new \DomainException('User with such email already exists.');
        }

        $user = new User(
            Id::next(),
            $command->email,
            $this->passwordsHasher->hash($this->passwordGenerator->generate()),
            new \DateTimeImmutable()
        );

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}