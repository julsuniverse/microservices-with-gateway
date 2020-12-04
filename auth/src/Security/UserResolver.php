<?php

declare(strict_types=1);

namespace App\Security;

use App\Service\PasswordHasher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\ScopeResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\Model\Scope;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Events;

final class UserResolver implements EventSubscriberInterface
{
    private $userProvider;
    private $hasher;

    public function __construct(UserProviderInterface $userProvider, PasswordHasher $hasher)
    {
        $this->userProvider = $userProvider;
        $this->hasher = $hasher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OAuth2Events::USER_RESOLVE => 'onUserResolve',
            OAuth2Events::SCOPE_RESOLVE => 'onScopeResolve',
        ];
    }

    public function onUserResolve(UserResolveEvent $event): void
    {
        //echo $event->getUsername(); die;
        try {
            $user = $this->userProvider->loadUserByUsername($event->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new \DomainException('User not found');
        }

        if (null === $user) {
            throw new \DomainException('User not found');
        }
        if (!$user->getPassword()) {
            throw new \DomainException('Invalid password');
        }
        if (!$this->hasher->validate($event->getPassword(), $user->getPassword())) {
            throw new \DomainException('Invalid password');
        }
        $event->setUser($user);
    }

    public function onScopeResolve(ScopeResolveEvent $event): void
    {
        try {
            $user = $this->userProvider->loadUserById($event->getUserIdentifier());
        } catch (UsernameNotFoundException $e) {
            throw new \DomainException('User not found');
        }

        $scopes = [];
        foreach ($user->getRoles() as $role) {
            $scopes[] = new Scope($role);
        }
        $event->setScopes(...$scopes);
    }
}
