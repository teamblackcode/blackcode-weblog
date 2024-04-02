<?php

namespace Tests\Unit;

use App\Database\PDODatabaseConnection;
use App\Database\QueryBuilder;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class QueryBuilderForArticlesTableTest extends TestCase
{
    private $queryBuilder;
    private $table;

    public function setUp(): void
    {
        $pdoConnection = new PDODatabaseConnection($this->getConfig());
        $this->queryBuilder = new QueryBuilder($pdoConnection->connect());
        $this->table = 'posts';
        $this->queryBuilder->beginTransaction();
        parent::setUp();
    }

    public function testItCanCreatePost()
    {
        $result = $this->insertIntoDb(['status' => 1]);
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testItCanUpdatePost()
    {
        $this->multipleInsertIntoDb(5, ['status' => 4]);
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 2)
            ->update(['status' => 3]);
        $this->assertEquals(5, $result);
    }

    public function testItCanUpdateWithMultipleWhere()
    {
        $this->multipleInsertIntoDb(5, ['status' => 4]);
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('categorie_id', 1)
            ->where('status', 2)
            ->update(['status' => 1, 'title' => "updated"]);
        $this->assertEquals(5, $result);
    }

    public function testItCanGetPost()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $this->multipleInsertIntoDb(5, ['status' => 3]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 3)
            ->get();
        $this->assertIsArray($result);
        $this->assertNotNull($result);
        $this->assertObjectHasProperty('id', $result[0]);
        $this->assertObjectHasProperty('title', $result[0]);
        $this->assertObjectHasProperty('content_text', $result[0]);
        $this->assertObjectHasProperty('image_id', $result[0]);
        $this->assertObjectHasProperty('author', $result[0]);
        $this->assertObjectHasProperty('categorie_id', $result[0]);
        $this->assertObjectHasProperty('status', $result[0]);
        $this->assertObjectHasProperty('created_at', $result[0]);
        $this->assertObjectHasProperty('updated_at', $result[0]);
    }

    public function testItCanFetchSpecificColumn()
    {
        $this->multipleInsertIntoDb(5, ['status' => 2]);
        $this->multipleInsertIntoDb(5, ['status' => 1]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 1)
            ->get(['title', 'content_text', 'author']);
        $this->assertIsArray($result);
        $this->assertNotNull($result);
        $this->assertObjectHasProperty('title', $result[0]);
        $this->assertObjectHasProperty('content_text', $result[0]);
        $this->assertObjectHasProperty('author', $result[0]);
    }

    public function testItCanGetFirstRow()
    {
        $this->multipleInsertIntoDb(5, ['status' => 3]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 3)
            ->first();
        $this->assertNotNull($result);
        $this->assertObjectHasProperty('id', $result);
        $this->assertObjectHasProperty('title', $result);
        $this->assertObjectHasProperty('content_text', $result);
        $this->assertObjectHasProperty('image_id', $result);
        $this->assertObjectHasProperty('author', $result);
        $this->assertObjectHasProperty('categorie_id', $result);
        $this->assertObjectHasProperty('status', $result);
        $this->assertObjectHasProperty('created_at', $result);
        $this->assertObjectHasProperty('updated_at', $result);
    }

    public function testFindbyMethodForGetPost()
    {
        $this->multipleInsertIntoDb(5, ['status' => 4]);
        $this->multipleInsertIntoDb(1, ['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->findBy('status', 2);
        $this->assertIsObject($result);
        $this->assertEquals(2, $result->status);
        $this->assertObjectHasProperty('id', $result);
        $this->assertObjectHasProperty('title', $result);
        $this->assertObjectHasProperty('content_text', $result);
        $this->assertObjectHasProperty('image_id', $result);
        $this->assertObjectHasProperty('author', $result);
        $this->assertObjectHasProperty('categorie_id', $result);
        $this->assertObjectHasProperty('status', $result);
        $this->assertObjectHasProperty('created_at', $result);
        $this->assertObjectHasProperty('updated_at', $result);
    }

    public function testItCanFindPostWithId()
    {
        $this->insertIntoDb(['status' => 3]);
        $id = $this->insertIntoDb(['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->find($id);
        $this->assertIsObject($result);
        $this->assertEquals($id, $result->id);
    }

    public function testItCanDeleteRecord()
    {
        $this->multipleInsertIntoDb(5, ['status' => 3]);
        $id = $this->insertIntoDb(['status' => 2]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('id', $id)
            ->delete();
        $this->assertEquals(1, $result);
    }

    public function testItCanGetPostCount()
    {
        $this->multipleInsertIntoDb(10, ['status' => 3]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->count();
        $this->assertIsArray($result);
        $this->assertEquals(10, $result['count']);
    }

    public function testItReturnsEmptyArrayWhenRecordNotFound()
    {
        $this->multipleInsertIntoDb(5, ['status' => 3]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', 5)
            ->get();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
    public function testItReturnsZeroWhenRecordNotFoundForUpdate()
    {
        $this->multipleInsertIntoDb(5, ['status' => 1]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', '3')
            ->update(['status' => 4]);
        $this->assertEquals(0, $result);
    }

    public function testItReturnsNullWhenFirstRecordNotFound()
    {
        $this->multipleInsertIntoDb(5, ['status' => 3]);
        $result = $this->queryBuilder
            ->table($this->table)
            ->where('status', '4')
            ->first();
        $this->assertNull($result);
    }


    public function getConfig()
    {
        return Config::get('database', 'pdo_testing');
    }

    public function insertIntoDb(array $options = [])
    {
        return $this->queryBuilder->table($this->table)->create($this->dataForPostTable($options));
    }

    private function multipleInsertIntoDb(int $count, array $options = [])
    {
        for ($i = 1; $i <= $count; $i++) {
            $this->insertIntoDb($options);
        }
    }

    private function dataForPostTable($options = [])
    {
        $article = $this->generatePost();
        return array_merge([
            'title'         => $article['title'],
            'content_text'  => $article['content'],
            'image_id'      => 1,
            'author'        => $article['author'],
            'categorie_id'  => 1,
        ], $options);
    }

    private function generatePost()
    {
        $titles = array(
            "The Importance of Time Management",
            "Exploring the Wonders of Nature",
            "The Rise of Artificial Intelligence",
            "The Benefits of Regular Exercise",
            "Understanding Quantum Mechanics"
        );

        $authors = array(
            "John Doe",
            "Jane Smith",
            "David Johnson",
            "Emily Brown",
            "Michael Wilson"
        );

        $randomTitle = array_rand($titles);
        $randomAuthor = array_rand($authors);
        $content = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta. Mauris massa. Vestibulum lacinia arcu eget nulla. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Curabitur sodales ligula in libero.Sed dignissim lacinia nunc. Curabitur tortor. Pellentesque nibh. Aenean quam. In scelerisque sem at dolor. Maecenas mattis. Sed convallis tristique sem. Proin ut ligula vel nunc egestas porttitor. Morbi lectus risus, iaculis vel, suscipit quis, luctus non, massa. Fusce ac turpis quis ligula lacinia aliquet. Mauris ipsum. Nulla metus metus, ullamcorper vel, tincidunt sed, euismod in, nibh.";

        return ['title' => $titles[$randomTitle], 'author' => $authors[$randomAuthor], 'content' => $content];
    }

    public function tearDown(): void
    {
        $this->queryBuilder->rollback();
        parent::tearDown();
    }
}
