<?php

declare(strict_types=1);

namespace App\Controller;

use OpenApi\Annotations as OA;
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
     *
     * @OA\Get (
     *     path="/user",
     *     tags={"Profile"},
     *     description="Show user",
     *     @OA\Response(
     *         response="200",
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="role", type="string"),
     *             @OA\Property(property="password_hash", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Domain Error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel400")
     *     ),
     *     security={{"Bearer": {}}}
     * )
     *
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
     *
     * @OA\Put(
     *     path="/user/edit",
     *     tags={"Profile"},
     *     description="Edit user",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email"},
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success response",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Domain Error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel400")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Errors",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModelValidation")
     *     ),
     *     security={{"Bearer": {}}}
     * )
     *
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
     *
     * @OA\Delete (
     *     path="/user",
     *     tags={"Profile"},
     *     description="Delete user",
     *     @OA\Response(
     *         response="204",
     *         description="Success response",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Domain Error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel400")
     *     ),
     *     security={{"Bearer": {}}}
     * )
     *
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