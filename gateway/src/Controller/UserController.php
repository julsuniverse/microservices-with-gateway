<?php

declare(strict_types=1);

namespace App\Controller;

use OpenApi\Annotations as OA;
use App\Annotations\AllowAccess;
use App\Exceptions\ValidationException;
use App\Repository\UserRepository;
use App\Service\UrlParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @AllowAccess(roles={"ROLE_ADMIN"})
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    private const CREATE_URL = '/user/create';
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
     * @Route("/user/create", name="user.create", methods={"POST"})
     *
     * @OA\Post(
     *     path="/user/create",
     *     tags={"User"},
     *     description="Create user",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email"},
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string")
     *         )
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
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $response = $this->repository->request(
            json_decode($request->getContent(), true),
            self::CREATE_URL,
            $request->getMethod()
        );
        return $this->json($response, Response::HTTP_CREATED);
    }

    /**
     * @Route("/user/{id}", name="user.show", methods={"GET"})
     *
     * @OA\Get (
     *     path="/user/{id}",
     *     tags={"User"},
     *     description="Show user",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
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
     * @param string $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $response = $this->repository->request(
            $request->request->all(),
            UrlParamConverter::convert(self::SHOW_URL, 'id', $id),
            $request->getMethod()
        );
        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @Route("/user/{id}/edit", name="user.edit", methods={"PUT"})
     *
     * @OA\Put(
     *     path="/user/{id}/edit",
     *     tags={"User"},
     *     description="Edit user",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
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
     * @param string $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function edit(Request $request, string $id): JsonResponse
    {
        $this->repository->request(
            json_decode($request->getContent(), true),
            UrlParamConverter::convert(self::EDIT_URL, 'id', $id),
            $request->getMethod()
        );
        return $this->json([], Response::HTTP_OK);
    }

    /**
     * @Route("/user/{id}", name="user.delete", methods={"DELETE"})
     *
     * @OA\Delete (
     *     path="/user/{id}",
     *     tags={"User"},
     *     description="Delete user",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
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
     * @param string $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function delete(Request $request, string $id): JsonResponse
    {
        $this->repository->request(
            json_decode($request->getContent(), true),
            UrlParamConverter::convert(self::DELETE_URL, 'id', $id),
            $request->getMethod()
        );
        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}