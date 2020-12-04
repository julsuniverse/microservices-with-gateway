<?php

declare(strict_types=1);

namespace App\Controller;

use OpenApi\Annotations as OA;
use App\Annotations\AllowAccess;
use App\Exceptions\ValidationException;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @AllowAccess(roles={"ROLE_GUEST"})
 *
 * @OA\Info(
 *     version="1.0.0",
 *     title="Test Project API",
 *     description="HTTP JSON API",
 * ),
 * @OA\Server(
 *     url="http://127.0.0.1:8080"
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="Bearer",
 *     type="apiKey",
 *     name="Authorization",
 *     in="header"
 * ),
 * @OA\Schema(
 *     schema="ErrorModel400",
 *     type="object",
 *     @OA\Property(property="error", type="object",
 *         @OA\Property(property="code", type="integer"),
 *         @OA\Property(property="message", type="string"),
 *     )
 * ),
 * @OA\Schema(
 *     schema="ErrorModelValidation",
 *     type="object",
 *     @OA\Property(property="error", type="object",
 *         @OA\Property(property="code", type="integer"),
 *         @OA\Property(property="message", type="string"),
 *         @OA\Property(property="violations", type="object"),
 *     )
 * )
 *
 * Class AuthController
 * @package App\Controller
 */
class AuthController extends AbstractController
{
    private const REGISTER_URL = '/register';
    private const LOGIN_URL = '/token';

    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     *
     * @OA\Post(
     *     path="/register",
     *     tags={"Auth"},
     *     description="Register",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email"},
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
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
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        $this->repository->request(
            json_decode($request->getContent(), true),
            self::REGISTER_URL,
            $request->getMethod()
        );
        return $this->json([], Response::HTTP_CREATED);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     *
     * @OA\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     description="Login",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"username", "password", "grant_type"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="grant_type", type="string", description="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token_type", type="string"),
     *             @OA\Property(property="expires_in", type="integer"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="refresh_token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Domain Error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel400")
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $response = $this->repository->token(
            json_decode($request->getContent(), true),
            self::LOGIN_URL,
            $request->getMethod()
        );
        return $this->json($response, Response::HTTP_OK);
    }
}