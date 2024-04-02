<?php

namespace Tests\Unit;

use App\Database\PDODatabaseConnection;
use App\Database\QueryBuilder;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class QueryBuilderForCommentsTableTest extends TestCase
{
    protected $queryBuilder;
    protected $table;

    public function setUp(): void
    {
        $pdoConnection = new PDODatabaseConnection($this->getConfig());
        $this->queryBuilder = new QueryBuilder($pdoConnection->connect());
        $this->table = 'comments';
        $this->queryBuilder->beginTransaction();
        parent::setUp();
    }

    public function testItCanCreateComment()
    {
        $result = $this->insertIntoDb(['status' => 2]);
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testItCanCreateCommentWithReplyId()
    {
        $id = $this->insertIntoDb(['status' => 2]);
        $result = $this->insertIntoDb(['status' => 2, 'reply_id' => $id]);
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testItCanUpdateComment()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 2)
            ->update(['status' => 1]);
        $this->assertIsInt($result);
        $this->assertEquals(5, $result);
    }

    public function testItCanUpdateWithMultipleWhere()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2, 'reply_id' => 1]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 2)
            ->where('reply_id', 1)
            ->update(['status' => 1, 'text' => 'This is a test comment for test with multiple (where)']);
        $this->assertIsInt($result);
        $this->assertEquals(5, $result);
    }

    public function testItCanUpdateReplyIdOfComments()
    {
        $id = $this->insertIntoDb(['status' => 1, 'text' => 'This is a test comment for update reply id method']);
        $this->insertIntoDb(['status' => 2, 'text' => 'This is a test comment for reply', 'user_id' => 1]);
        $this->multipleInsertIntoDb(5, ['status' => 3]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 2)
            ->where('user_id', 1)
            ->update(['status' => 1, 'reply_id' => $id]);
        $this->assertIsInt($result);
        $this->assertEquals(1, $result);
    }

    public function testItCanGetComments()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 2)
            ->get();
        $this->assertIsArray($result);
        $this->assertNotNull($result);
        $this->assertObjectHasProperty('id', $result[0]);
        $this->assertObjectHasProperty('post_id', $result[0]);
        $this->assertObjectHasProperty('user_id', $result[0]);
        $this->assertObjectHasProperty('reply_id', $result[0]);
        $this->assertObjectHasProperty('text', $result[0]);
        $this->assertObjectHasProperty('profile_image_id', $result[0]);
        $this->assertObjectHasProperty('status', $result[0]);
        $this->assertObjectHasProperty('created_at', $result[0]);
    }

    public function testItCanFetchSpecificColumns()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 2)
            ->get(['text', 'status', 'created_at']);
        $this->assertIsArray($result);
        $this->assertNotNull($result);
        $this->assertObjectHasProperty('text', $result[0]);
        $this->assertObjectHasProperty('status', $result[0]);
        $this->assertObjectHasProperty('created_at', $result[0]);
    }

    public function testItCanGetFirstRow()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 2)
            ->first();
        $this->assertNotNull($result);
        $this->assertIsObject($result);
        $this->assertObjectHasProperty('id', $result);
        $this->assertObjectHasProperty('post_id', $result);
        $this->assertObjectHasProperty('user_id', $result);
        $this->assertObjectHasProperty('reply_id', $result);
        $this->assertObjectHasProperty('text', $result);
        $this->assertObjectHasProperty('profile_image_id', $result);
        $this->assertObjectHasProperty('status', $result);
        $this->assertObjectHasProperty('created_at', $result);
        $this->assertEquals(2, $result->status);
    }

    public function testFindbyMethodForGetComment()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $this->multipleInsertIntoDb(1, ['status' => 1]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->findBy('status', 1);
        $this->assertNotNull($result);
        $this->assertIsObject($result);
        $this->assertObjectHasProperty('id', $result);
        $this->assertObjectHasProperty('post_id', $result);
        $this->assertObjectHasProperty('user_id', $result);
        $this->assertObjectHasProperty('reply_id', $result);
        $this->assertObjectHasProperty('text', $result);
        $this->assertObjectHasProperty('profile_image_id', $result);
        $this->assertObjectHasProperty('status', $result);
        $this->assertObjectHasProperty('created_at', $result);
        $this->assertEquals(1, $result->status);
    }

    public function testItCanGetUserWithId()
    {
        $id = $this->insertIntoDb(['status' => 1]);
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->find($id);
        $this->assertIsObject($result);
        $this->assertEquals($id, $result->id);
    }

    public function testItCanDeleteRecorde()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2, 'reply_id' => 1]);
        $id = $this->insertIntoDb(['status' => 1]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 1)
            ->delete();
        $this->assertIsInt($result);
        $this->assertEquals(1, $result);
    }

    public function testItCanGetPostsCount()
    {
        $this->multipleInsertIntoDb(10);
        $result = $this->queryBuilder
            ->table($this->table)
            ->count();
        $this->assertIsArray($result);
        $this->assertEquals(10, $result['count']);
    }

    public function testItReturnsEmptyArrayWhenRecordNotFound()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $result  = $this->queryBuilder
            ->table($this->table)
            ->where('status', 3)
            ->get();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testItReturnsZeroWhenRecordNotFoundForUpdate()
    {
        $this->multipleInsertIntoDb(5, ['status' => 1]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 2)
            ->update(['status' => '3']);
        $this->assertEquals(0, $result);
    }

    public function testItReturnsNullWhenFirstRecordNotFound()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', '3')
            ->first();
        $this->assertNull($result);
    }

    private function dataForCommentsTable(array $options = [])
    {
        return array_merge([
            'post_id'           => 1,
            'user_id'           => 1,
            'text'              => 'This is a test comment text',
            'profile_image_id'  => 1,
        ], $options);
    }

    private function insertIntoDb($options = [])
    {
        return $this->queryBuilder->table($this->table)->create($this->dataForCommentsTable($options));
    }

    private function multipleInsertIntoDb(int $count, $options = [])
    {
        for ($i = 1; $i <= $count; $i++) {
            $this->insertIntoDb($options);
        }
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
