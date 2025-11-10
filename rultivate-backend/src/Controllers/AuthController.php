<?php
namespace Rultivate\Controllers;

use Rultivate\Services\AuthService;
use Rultivate\Utils\Response;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function registerCustomer(array $request): void
    {
        try {
            $result = $this->authService->registerCustomer($request['body']);
            $this->json($result, 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['message' => $e->getMessage()], 422);
        }
    }

    public function registerVendor(array $request): void
    {
        try {
            $result = $this->authService->registerVendor($request['body']);
            $this->json($result, 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['message' => $e->getMessage()], 422);
        }
    }

    public function login(array $request): void
    {
        try {
            $body = $request['body'];
            $result = $this->authService->login($body['email'], $body['password'], $body['role']);
            $this->json($result);
        } catch (\InvalidArgumentException $e) {
            $this->json(['message' => $e->getMessage()], 401);
        }
    }

    public function forgotPassword(array $request): void
    {
        $body = $request['body'];
        $result = $this->authService->forgotPassword($body['email']);
        $this->json($result);
    }

    public function resetPassword(array $request): void
    {
        try {
            $body = $request['body'];
            $result = $this->authService->resetPassword($body['token'], $body['password']);
            $this->json($result);
        } catch (\InvalidArgumentException $e) {
            $this->json(['message' => $e->getMessage()], 422);
        }
    }

    public function me(array $request): void
    {
        $user = $request['user'];
        $profile = $this->authService->me((int)$user['id']);
        $this->json($profile);
    }
}
