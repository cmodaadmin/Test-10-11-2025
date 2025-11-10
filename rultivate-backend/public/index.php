<?php
require __DIR__ . '/../vendor/autoload.php';

use Rultivate\Router;
use Rultivate\Middleware\AuthMiddleware;
use Rultivate\Middleware\RBACMiddleware;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$config = require __DIR__ . '/../config/config.php';
$router = new Router($config);

$router->use(new AuthMiddleware($config));
$router->use(new RBACMiddleware());

require __DIR__ . '/../src/routes.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
