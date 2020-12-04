<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotations\AllowAccess;
use App\Exceptions\ValidationException;
use App\Repository\UserRepository;
use App\Security\User;
use App\Service\UrlParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @AllowAccess(roles={"ROLE_ADMIN", "ROLE_USER"})
 * Class ProfileController
 * @package App\Controller
 */
class ProfileController extends AbstractController
{
    private const SHOW_URL = '/user/{id}';
    private const EDIT_URL = '/user/{id}/edit';
    private const DELETE_URL = '/user/{id}';

    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/user", name="profile.user.show", methods={"GET"})
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException
     */
    public function show(Request $request, User $user): JsonResponse
    {
        $response = $this->repository->request(
            $request->request->all(),
            UrlParamConverter::convert(self::SHOW_URL, 'id', $user->id),
            $request->getMethod()
        );
        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @Route("/user/edit", name="profile.user.edit", methods={"PUT"})
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException
     */
    public function edit(Request $request, User $user): JsonResponse
    {
        $this->repository->request(
            json_decode($request->getContent(), true),
            UrlParamConverter::convert(self::EDIT_URL, 'id', $user->id),
            $request->getMethod()
        );
        return $this->json([], Response::HTTP_OK);
    }

    /**
     * @Route("/user", name="profile.user.delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException
     */
    public function delete(Request $request, User $user): JsonResponse
    {
        $this->repository->request(
            json_decode($request->getContent(), true),
            UrlParamConverter::convert(self::DELETE_URL, 'id', $user->id),
            $request->getMethod()
        );
        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}