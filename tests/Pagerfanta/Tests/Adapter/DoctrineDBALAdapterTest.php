<?php

namespace Pagerfanta\Tests\Adapter;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\SchemaTool;
use Pagerfanta\Adapter\DoctrineDBALAdapter;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Portability\Connection;

class DoctrineDBALAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    private $conn;

    public function setUp()
    {
        parent::setUp();

        if (!class_exists('Doctrine\DBAL\DriverManager')) {
           $this->markTestSkipped('Doctrine DBAL is not available');
        }

        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $this->conn = \Doctrine\DBAL\DriverManager::getConnection($conn);

        $schema = new \Doctrine\DBAL\Schema\Schema();
        $posts = $schema->createTable('posts');
        $posts->addColumn('id', 'integer', array('unsigned' => true));
        $posts->addColumn('username', 'string', array('length' => 32));
        $posts->addColumn('post_content', 'text');
        $posts->setPrimaryKey(array('id'));

        $comments = $schema->createTable('comments');
        $comments->addColumn('id', 'integer', array('unsigned' => true));
        $comments->addColumn('post_id', 'integer', array('unsigned' => true));
        $comments->addColumn('username', 'string', array('length' => 32));
        $comments->addColumn('content', 'text');
        $comments->setPrimaryKey(array('id'));

        $queries = $schema->toSql(new \Doctrine\DBAL\Platforms\SqlitePlatform()); // get queries to create this schema.

        foreach ($queries as $sql) {
            $this->conn->executeQuery($sql);
        }

        for ($i = 1; $i <= 50; $i++) {
            $this->conn->insert('posts', array('username' => 'Jon Doe', 'post_content' => 'Post #' . $i));
        }

        for ($i = 1; $i <= 50; $i++) {
            for ($j = 1; $j <= 5; $j++) {
                $this->conn->insert('comments', array('post_id' => $i, 'username' => 'Jon Doe', 'content' => 'Comment #' . $j));
            }
        }
    }

    public function testAdapterCount()
    {
        $query = new QueryBuilder($this->conn);
        $query->select('p.*')
            ->from('posts', 'p')
        ;

        $adapter = new DoctrineDBALAdapter($query, 'p.id');

        $this->assertEquals(50, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $query = new QueryBuilder($this->conn);
        $query->select('p.*')
            ->from('posts', 'p')
        ;

        $adapter = new DoctrineDBALAdapter($query, 'p.id');
        $this->assertEquals(10, count($adapter->getSlice(0, 10)));
        $this->assertEquals(1, count($adapter->getSlice(0, 1)));
        $this->assertEquals(1, count($adapter->getSlice(1, 1)));
    }

    public function testCountAfterSlice()
    {
        $query = new QueryBuilder($this->conn);
        $query->select('p.*')
            ->from('posts', 'p')
        ;

        $adapter = new DoctrineDBALAdapter($query, 'p.id');
        $adapter->getSlice(0, 1);
        $this->assertEquals(50, $adapter->getNbResults());
    }

    public function testAdapterCountFetchJoin()
    {
        $query = new QueryBuilder($this->conn);
        $query->select('p.*')
            ->from('posts', 'p')
            ->innerJoin('p', 'comments', 'c', 'c.post_id = p.id')
        ;

        $adapter = new DoctrineDBALAdapter($query, 'p.id');
        $this->assertEquals(50, $adapter->getNbResults());
    }
}
