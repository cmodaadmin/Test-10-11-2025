<?php
namespace Rultivate\Middleware;

use Rultivate\Utils\Response;
use Rultivate\Utils\Database;
use Rultivate\Utils\JWT;
use Rultivate\Services\UserService;

class AuthMiddleware
{
    private Database $database;
    private JWT $jwt;
    private UserService $userService;

    public function __construct(array $config)
    {
        $this->database = new Database($config['db']);
        $this->jwt = new JWT($config['jwt']);
        $this->userService = new UserService($this->database);
    }

    public function handle(array $request, callable $next, array $options)
    {
        if (!($options['auth'] ?? false)) {
            $request['db'] = $this->database;
            return $next($request);
        }

        $headers = $request['headers'];
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            Response::json(['message' => 'Unauthorized'], 401);
        }
        $token = trim(substr($authHeader, 7));
        try {
            $decoded = $this->jwt->verify($token);
        } catch (\Exception $e) {
            Response::json(['message' => 'Invalid token'], 401);
        }
        $user = $this->userService->findById((int)$decoded['sub']);
        if (!$user) {
            Response::json(['message' => 'User not found'], 401);
        }
        $request['user'] = $user;
        $request['db'] = $this->database;
        return $next($request);
    }
}
