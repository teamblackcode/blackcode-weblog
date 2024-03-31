<?php

namespace Tests\Unit;

use App\Contracts\QueryBuilderInterface;
use App\Database\PDODatabaseConnection;
use App\Database\QueryBuilder;
use App\Helpers\Config;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

class QueryBuilderForUsersTableTest extends TestCase
{
    private $queryBuilder;

    public function setUp(): void
    {
        $pdoConnection = new PDODatabaseConnection($this->getConfig());
        $this->queryBuilder = new QueryBuilder($pdoConnection->connect());
        $this->queryBuilder->beginTransaction();
        parent::setUp();
    }

    public function testItCanCreateData()
    {
        $result = $this->insertIntoDb(['role' => 2]);
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testItCanUpdateData()
    {
        $this->multipleInsertIntoDb(10, ['role' => 1]);
        $result = $this->queryBuilder
            ->table('users')
            ->where('email', 'mahdishahipro@gmail.com')
            ->update(['email' => 'mahdishahiwindows@gmail.com', 'fullname' => 'mohaddese panahi']);
        $this->assertEquals(10, $result);
    }

    public function testItCanUpdateWithMultipleWhere()
    {
        $this->multipleInsertIntoDb(10, ['role' => 1]);
        $result = $this->queryBuilder
            ->table('users')
            ->where('email', 'mahdishahipro@gmail.com')
            ->where('fullname', 'mahdishahi')
            ->update(['email' => 'mahdishahiwindows@gmail.com', 'fullname' => 'mohaddese panahi']);
        $this->assertEquals(10, $result);
    }

    public function testItCanGetData()
    {
        $this->multipleInsertIntoDb(5, ['role' => 1]);
        $this->multipleInsertIntoDb(5, ['role' => 2]);
        $this->multipleInsertIntoDb(5, ['role' => 3]);
        $result = $this->queryBuilder
            ->table('users')
            ->where('role', 2)
            ->where('email', 'mahdishahipro@gmail.com')
            ->get();
        $this->assertIsArray($result);
        $this->assertNotNull($result);
    }

    public function testItCanFetchSpecificColumn()
    {
        $this->multipleInsertIntoDb(10, ['role' => 2]);
        $result = $this->queryBuilder
            ->table('users')
            ->where('email', 'mahdishahipro@gmail.com')
            ->where('fullname', 'mahdishahi')
            ->get(['fullname', 'email', 'profile_image_id']);
        $this->assertIsArray($result);
        $this->assertObjectHasProperty('fullname', $result[0]);
        $this->assertObjectHasProperty('email', $result[0]);
        $this->assertObjectHasProperty('profile_image_id', $result[0]);
        $result = json_decode(json_encode($result[0]), true);
        $this->assertEquals(['fullname', 'email', 'profile_image_id'], array_keys($result));
    }

    public function testItCanGetFirstRow()
    {
        $this->multipleInsertIntoDb(5, ['role' => 2]);
        $result = $this->queryBuilder
            ->table('users')
            ->where('email', 'mahdishahipro@gmail.com')
            ->first();
        $this->assertNotNull($result);
        $this->assertObjectHasProperty('fullname', $result);
        $this->assertObjectHasProperty('email', $result);
        $this->assertObjectHasProperty('password', $result);
        $this->assertObjectHasProperty('profile_image_id', $result);
        $this->assertObjectHasProperty('role', $result);
        $this->assertObjectHasProperty('created_at', $result);
        $this->assertEquals(2, $result->role);
    }

    public function testFindbyMethodForGetData()
    {
        $this->multipleInsertIntoDb(5, ['role' => 2]);
        $this->multipleInsertIntoDb(1, ['role' => 1]);
        $result = $this->queryBuilder
            ->table('users')
            ->findBy('role', 1);
        $this->assertIsObject($result);
        $this->assertEquals(1, $result->role);
        $this->assertObjectHasProperty('fullname', $result);
        $this->assertObjectHasProperty('email', $result);
        $this->assertObjectHasProperty('password', $result);
        $this->assertObjectHasProperty('profile_image_id', $result);
        $this->assertObjectHasProperty('role', $result);
        $this->assertObjectHasProperty('created_at', $result);
    }

    public function testItCanFindDataWithId()
    {
        $this->insertIntoDb(['role' => 1]);
        $id = $this->insertIntoDb(['role' => 3]);
        $result = $this->queryBuilder
            ->table('users')
            ->find($id);
        $this->assertIsObject($result);
        $this->assertEquals($id, $result->id);
    }

    public function testItCanDeleteRecord()
    {
        $this->multipleInsertIntoDb(5, ['role' => 2, 'fullname' => 'mahyarshahi']);
        $id = $this->insertIntoDb(['role' => 1]);
        $result = $this->queryBuilder
            ->table('users')
            ->where('fullname', 'mahdishahi')
            ->delete();
        $this->assertEquals(1, $result);
    }

    public function testItCanGetUsersCount()
    {
        $this->multipleInsertIntoDb(10);
        $result = $this->queryBuilder
            ->table('users')
            ->count();
        $this->assertIsArray($result);
        $this->assertEquals(10, $result['count']);
    }

    public function testItReturnsEmptyArrayWhenRecordNotFound()
    {
        $this->multipleInsertIntoDb(5, ['role' => 3]);
        $result  = $this->queryBuilder
            ->table('users')
            ->where('fullname', 'clkewrjaq')
            ->get();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testItReturnsZeroWhenRecordNotFoundForUpdate()
    {
        $this->multipleInsertIntoDb(5, ['role' => 3]);
        $result = $this->queryBuilder
            ->table('users')
            ->where('fullname', 'okjo')
            ->update(['fullname' => 'test']);
        $this->assertEquals(0, $result);
    }

    public function testItReturnsNullWhenFirstRecordNotFound()
    {
        $this->multipleInsertIntoDb(5, ['role' => 2]);
        $result = $this->queryBuilder
            ->table('users')
            ->where('fullname', 'mahdi')
            ->first();
        $this->assertNull($result);
    }

    private function insertIntoDb(array $options = [])
    {
        $data = $this->dataForUsersTable($options);
        return $this->queryBuilder
            ->table('users')
            ->create($data);
    }

    private function multipleInsertIntoDb(int $count, array $options = [])
    {
        for ($i = 1; $i <= $count; $i++) {
            $this->insertIntoDb($options);
        }
    }

    private function dataForUsersTable($options = [])
    {
        return array_merge([
            'fullname'         => 'mahdishahi',
            'email'            => 'mahdishahipro@gmail.com',
            'password'         => 'Mahdi1',
            'profile_image_id' => 1,
        ], $options);
    }

    private function getConfig()
    {
        return Config::get('database', 'pdo_testing');
    }

    public function tearDown(): void
    {
        $this->queryBuilder->rollback();
        parent::tearDown();
    }
}
