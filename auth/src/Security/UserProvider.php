<?php

declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\AuthDTO;
use App\Repository\AuthRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $users;

    public function __construct(AuthRepository $users)
    {
        $this->users = $users;
    }

    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->loadUser($username);
        return self::identityByUser($user);
    }

    public function refreshUser(UserInterface $identity): UserInterface
    {
        if (!$identity instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . \get_class($identity));
        }

        $user = $this->loadUser($identity->getUsername());
        return self::identityByUser($user);
    }

    public function supportsClass($class): bool
    {
        return $class === UserIdentity::class;
    }

    private function loadUser(string $username): AuthDTO
    {
        if ($user = $this->users->findByEmail($username)) {
            return $user;
        }

        throw new UsernameNotFoundException('');
    }

    public function loadUserById(string $id): UserIdentity
    {
        if ($user = $this->users->findById($id)) {
            return self::identityByUser($user);
        }

        throw new UsernameNotFoundException('');
    }

    private static function identityByUser(AuthDTO $user): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->id,//$user->email,
            $user->password_hash,
            $user->role
        );
    }
}
