<?php
namespace Rultivate\Utils;

use PDO;
use PDOException;

class Database
{
    private PDO $connection;

    public function __construct(array $config)
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['database'], $config['charset']);
        $this->connection = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function transaction(callable $callback)
    {
        try {
            $this->connection->beginTransaction();
            $result = $callback($this->connection);
            $this->connection->commit();
            return $result;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}
