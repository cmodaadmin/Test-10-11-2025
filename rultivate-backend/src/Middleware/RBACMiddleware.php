<?php
namespace Rultivate\Middleware;

use Rultivate\Utils\Response;

class RBACMiddleware
{
    public function handle(array $request, callable $next, array $options)
    {
        $requiredRoles = $options['roles'] ?? [];
        if (!$requiredRoles) {
            return $next($request);
        }
        $user = $request['user'] ?? null;
        if (!$user) {
            Response::json(['message' => 'Unauthorized'], 401);
        }
        $roles = $user['roles'] ?? [];
        $allowed = array_intersect($roles, $requiredRoles);
        if (empty($allowed)) {
            Response::json(['message' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}
