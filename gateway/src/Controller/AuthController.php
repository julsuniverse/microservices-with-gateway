<?php

declare(strict_types=1);

namespace App\Controller;

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