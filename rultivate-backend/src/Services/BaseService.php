<?php
namespace Rultivate\Services;

use Rultivate\Utils\Database;
use PDO;

abstract class BaseService
{
    protected PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }
}
