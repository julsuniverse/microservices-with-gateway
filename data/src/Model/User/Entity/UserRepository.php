<?php

declare(strict_types=1);

namespace App\Model\User\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class UserRepository
{
    private $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    public function get(string $id): User
    {
        /** @var User $user */
        if (!$user = $this->repo->find($id)) {
            throw new EntityNotFoundException('User is not found');
        }
        return $user;
    }

    public function hasByEmail(string $email): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}