<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Delete;

use App\Model\User\Entity\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    public function handle(Command $command)
    {
        $user = $this->userRepository->get($command->id->getValue());
        $this->em->remove($user);
        $this->em->flush();
    }
}