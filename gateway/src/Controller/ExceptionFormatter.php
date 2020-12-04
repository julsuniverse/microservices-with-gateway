<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exceptions\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionFormatter implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException'
        );
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof \DomainException) {
            $event->setResponse(new JsonResponse([
                'error' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => $exception->getMessage(),
                ]
            ], Response::HTTP_BAD_REQUEST));
        }

        if ($exception instanceof UnauthorizedHttpException) {
            $event->setResponse(new JsonResponse([
                'error' => [
                    'code' => Response::HTTP_UNAUTHORIZED,
                    'message' => $exception->getMessage(),
                ]
            ], Response::HTTP_UNAUTHORIZED));
        }

        if ($exception instanceof ValidationException) {
            $errors = [];

            foreach ($exception->getViolations() as $violation) {
                $errors[$violation->propertyPath] = $violation->title;
            }
            $event->setResponse(new JsonResponse([
                'error' => [
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $exception->getMessage(),
                    'violations' => $errors,
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }
}
