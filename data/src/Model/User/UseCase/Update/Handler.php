<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Update;

use App\Model\User\Entity\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function handle(Command $command)
    {
        $user = $this->userRepository->get($command->id->getValue());

        $user->edit(
            $command->email
        );

        $this->em->flush();
    }
}