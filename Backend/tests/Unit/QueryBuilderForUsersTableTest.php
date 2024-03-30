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

    private function insertIntoDb(array $options = [])
    {
        $data = $this->dataForUsersTable($options);
        return $this->queryBuilder
            ->table('users')
            ->create($data);
    }

    private function multipleInsertIntoDb(int $count, array $options)
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

    private function dataForPostTable($options = [])
    {
        $article = $this->generateArticles();
        return array_merge([
            'title'         => $article['title'],
            'content_text'  => $article['content'],
            'image_id'      => 1,
            'author'        => $article['author'],
            'categorie_id'  => 1,
        ], $options);
    }

    private function dataForCommentsTable($options)
    {
        return array_merge([
            'post_id'           => 1,
            'user_id'           => 1,
            'text'              => 'This is a test comment text',
            'profile_image_id'  => 1,
        ], $options);
    }

    private function generateArticles()
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

        return ['title' => $randomTitle, 'author' => $randomAuthor, 'content' => $content];
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
