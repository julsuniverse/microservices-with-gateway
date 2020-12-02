<?php

declare(strict_types=1);

namespace App\Model\User\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class UserRepository
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(User::class);
    }

    public function get(string $id): User
    {
        /** @var User $user */
        if (!$user = $this->repository->find($id)) {
            throw new EntityNotFoundException('User is not found');
        }
        return $user;
    }

    public function hasByEmail(string $email): bool
    {
        return $this->repository->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}