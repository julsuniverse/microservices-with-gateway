<?php

declare(strict_types=1);

namespace App\Event;

use App\Annotations\AllowAccess;
use App\Security\User;
use App\Service\TokenDecoder;
use Doctrine\Common\Annotations\Reader;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class RequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var TokenDecoder
     */
    private $tokenDecoder;

    public function __construct(Reader $reader, TokenDecoder $tokenDecoder)
    {
        $this->reader = $reader;
        $this->tokenDecoder = $tokenDecoder;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(ControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $target = $event->getController();
        [$controller] = $target;
        /** @var AllowAccess $allowAccess */
        $allowAccess = $this->reader->getClassAnnotation(
            new \ReflectionObject($controller),
            AllowAccess::class
        );

        $token = $request->headers->get('Authorization');

        if (!$token && (!$allowAccess || $allowAccess->isGuest())) {
            return;
        }

        $token = trim(str_replace('Bearer', '', $token));
        $decoded = $this->tokenDecoder->decode($token);

        $id = $decoded->sub ?? null;
        $roles = $decoded->scopes ?? null;

        if (!$id || !$roles) {
            throw new \RuntimeException('Access denied');
        }

        $this->checkRoles($allowAccess, $roles);

        $request->attributes->set('user', new User($id, $roles));

    }

    private function checkRoles(AllowAccess $allowAccess, array $roles): void
    {
        $allowed = false;
        foreach ($roles as $role) {
            if (in_array($role, $allowAccess->roles)) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            throw new \RuntimeException('Access denied');
        }
    }
}