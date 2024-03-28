<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Exceptions\ConfigNotValidException;
use App\Exceptions\DatabaseConnectionException;
use PDO;
use PDOException;

class PDODatabaseConnection implements DatabaseConnectionInterface
{
    protected $config;
    protected $connection;

    protected const REQUIRED_CONFIG_KEYS = [
        'driver',
        'host',
        'database',
        'username',
        'password'
    ];

    public function __construct($config)
    {
        if (!$this->isConfigValid($config)) {
            throw new ConfigNotValidException();
        }
        $this->config = $config;
    }

    public function connect()
    {
        $dsn = $this->generateDsn($this->config);
        try {
            $this->connection = new PDO(...$dsn);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $error) {
            throw new DatabaseConnectionException();
        }
        return $this;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function generateDsn(array $config)
    {
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']};";
        return [$dsn, $config['username'], $config['password']];
    }

    public function isConfigValid(array $config)
    {
        $matches = array_intersect(self::REQUIRED_CONFIG_KEYS, array_keys($config));
        return count($matches) === count(self::REQUIRED_CONFIG_KEYS);
    }
}
