<?php

namespace Pagerfanta\Tests\Adapter;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Schema;
use Pagerfanta\Adapter\DoctrineDBALAdapter;

class DoctrineDBALAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $conn;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Doctrine\DBAL\DriverManager')) {
            $this->markTestSkipped('Doctrine DBAL is not available');
        }

        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $this->conn = DriverManager::getConnection($conn);

        $schema = new Schema();
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

        $queries = $schema->toSql($this->conn->getDatabasePlatform()); // get queries to create this schema.

        foreach ($queries as $sql) {
            $this->conn->executeQuery($sql);
        }

        for ($i = 1; $i <= 50; $i++) {
            $this->conn->insert('posts', array('username' => 'Jon Doe', 'post_content' => 'Post #' . $i));
            for ($j = 1; $j <= 5; $j++) {
                $this->conn->insert('comments', array('post_id' => $i, 'username' => 'Jon Doe', 'content' => 'Comment #' . $j));
            }
        }

        $this->queryBuilder = new QueryBuilder($this->conn);
    }

    public function testAdapterCount()
    {
        $this->queryBuilder->select('p.*')
            ->from('posts', 'p')
        ;

        $adapter = new DoctrineDBALAdapter($this->queryBuilder, 'p.id');

        $this->assertEquals(50, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $this->queryBuilder->select('p.*')
            ->from('posts', 'p')
        ;

        $adapter = new DoctrineDBALAdapter($this->queryBuilder, 'p.id');
        $this->assertEquals(10, count($adapter->getSlice(0, 10)));
        $this->assertEquals(1, count($adapter->getSlice(0, 1)));
        $this->assertEquals(1, count($adapter->getSlice(1, 1)));
    }

    public function testCountAfterSlice()
    {
        $this->queryBuilder->select('p.*')
            ->from('posts', 'p')
        ;

        $adapter = new DoctrineDBALAdapter($this->queryBuilder, 'p.id');
        $adapter->getSlice(0, 1);
        $this->assertEquals(50, $adapter->getNbResults());
    }

    public function testAdapterCountFetchJoin()
    {
        $this->queryBuilder->select('p.*')
            ->from('posts', 'p')
            ->innerJoin('p', 'comments', 'c', 'c.post_id = p.id')
        ;

        $adapter = new DoctrineDBALAdapter($this->queryBuilder, 'p.id');
        $this->assertEquals(50, $adapter->getNbResults());
    }

    /**
     * @expectedException Pagerfanta\Exception\LogicException
     */
    public function testAdapterNoAliasInCountField()
    {
        $adapter = new DoctrineDBALAdapter($this->queryBuilder, 'id');
    }
}
