<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User\Entity\User;
use App\Model\User\UseCase\Create;
use App\Model\User\UseCase\Update;
use App\Model\User\UseCase\Delete;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/user/create", name="user.create", methods={"POST"})
     * @param Request $request
     * @param Create\Handler $handler
     * @return JsonResponse
     */
    public function create(Request $request, Create\Handler $handler): JsonResponse
    {
        /** @var Create\Command $command */
        $command = $this->serializer->deserialize($request->getContent(), Create\Command::class, 'json');

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        return $this->json([], Response::HTTP_CREATED);
    }

    /**
     * @Route("/user/{id}", name="user.show", methods={"GET"})
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return $this->json([
            'id' => $user->getId()->getValue(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()->getName()
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/user/{id}/edit", name="user.edit", methods={"PATCH"})
     * @param User $user
     * @param Request $request
     * @param Update\Handler $handler
     * @return JsonResponse
     */
    public function edit(User $user, Request $request, Update\Handler $handler): JsonResponse
    {
        /** @var Update\Command $command */
        $command = $this->serializer->deserialize($request->getContent(), Update\Command::class, 'json', [
            'object_to_populate' => new Update\Command($user),
            'ignored_attributes' => ['id'],
        ]);

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        return $this->json([], Response::HTTP_OK);
    }

    /**
     * @Route("/user/{id}", name="user.delete", methods={"DELETE"})
     * @param User $user
     * @param Delete\Handler $handler
     * @return JsonResponse
     */
    public function delete(User $user, Delete\Handler $handler): JsonResponse
    {
        $command = new Delete\Command($user);
        $handler->handle($command);

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

}