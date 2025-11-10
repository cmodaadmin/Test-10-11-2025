<?php
namespace Rultivate\Controllers;

use Rultivate\Utils\Response;

abstract class BaseController
{
    protected function json($data, int $status = 200): void
    {
        Response::json($data, $status);
    }
}
