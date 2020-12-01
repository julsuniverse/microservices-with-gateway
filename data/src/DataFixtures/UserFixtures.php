<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\User\Entity\Id;
use App\Model\User\Entity\Role;
use App\Model\User\Entity\User;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $password = $this->hasher->hash('password');

        $user = new User(
            Id::next(),
            'admin@email.com',
            $password,
            new \DateTimeImmutable()
        );

        $user->changeRole(Role::admin());

        $manager->persist($user);
        $manager->flush();
    }
}