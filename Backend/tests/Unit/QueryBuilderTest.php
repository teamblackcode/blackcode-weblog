<?php

namespace Tests\Unit;

use App\Contracts\QueryBuilderInterface;
use App\Database\PDODatabaseConnection;
use App\Database\QueryBuilder;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    public function testQueryBuilderShouldBeImplementQueryBuilderInterface()
    {
        $pdoConneciton = new PDODatabaseConnection($this->getConfig());

        $queryBuilder = new QueryBuilder($pdoConneciton->connect());

        $this->assertInstanceOf(QueryBuilderInterface::class, $queryBuilder);
    }

    private function getConfig()
    {
        return Config::get('database', 'pdo_testing');
    }
}
