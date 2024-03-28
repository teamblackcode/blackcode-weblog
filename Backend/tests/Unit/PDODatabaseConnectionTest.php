<?php

namespace Tests\Unit;

use App\Contracts\DatabaseConnectionInterface;
use App\Database\PDODatabaseConnection;
use App\Exceptions\ConfigNotValidException;
use App\Exceptions\DatabaseConnectionException;
use App\Helpers\Config;
use PDO;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

class PDODatabaseConnectionTest extends TestCase
{
    public function testPDODatabaseConnectionImplementPDODatabaseConnectionInterface()
    {
        $config = $this->getConfig();

        $pdoConnection = new PDODatabaseConnection($config);

        $this->assertInstanceOf(DatabaseConnectionInterface::class, $pdoConnection);
    }

    public function testConnectMethodShouldReturnsValidInstance()
    {
        $config = $this->getConfig();

        $pdoConnection = new PDODatabaseConnection($config);

        $pdoHandler = $pdoConnection->connect();

        $this->assertInstanceOf(PDODatabaseConnection::class, $pdoHandler);

        return $pdoHandler;
    }

    #[Depends('testConnectMethodShouldReturnsValidInstance')]
    public function testGetConnectionMethodShouldBeConnectToDatabase($pdoHandler)
    {
        $this->assertInstanceOf(PDO::class, $pdoHandler->getConnection());
    }

    public function testItThrowExceptionIfCofigIsInvalid()
    {
        $this->expectException(DatabaseConnectionException::class);

        $config = $this->getConfig();

        $config['database'] = 'database';

        $pdoConnection = new PDODatabaseConnection($config);

        $pdoConnection->connect();
    }

    public function testReceivedConfigHaveRequiredKey()
    {
        $this->expectException(ConfigNotValidException::class);

        $config = $this->getConfig();

        unset($config['database']);

        $pdoConnection = new PDODatabaseConnection($config);

        $pdoConnection->connect();
    }

    private function getConfig()
    {
        return Config::get('database', 'pdo_testing');
    }
}
