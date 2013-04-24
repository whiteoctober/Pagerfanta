<?php

namespace Pagerfanta\Tests\Adapter;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Schema;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

abstract class DoctrineDbalTestCase extends \PHPUnit_Framework_TestCase
{
    protected $qb;

    protected function setUp()
    {
        if ($this->isDoctrineDbalNotAvailable()) {
            $this->markTestSkipped('Doctrine DBAL is not available');
        }

        $conn = $this->getConnection();

        $this->createSchema($conn);
        $this->insertData($conn);

        $this->qb = new QueryBuilder($conn);
        $this->qb->select('p.*')->from('posts', 'p');
    }

    private function isDoctrineDbalNotAvailable()
    {
        return !class_exists('Doctrine\DBAL\DriverManager');
    }

    private function getConnection()
    {
        $params = $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true
        );

        return DriverManager::getConnection($params);
    }

    private function createSchema($conn)
    {
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

        $queries = $schema->toSql($conn->getDatabasePlatform()); // get queries to create this schema.

        foreach ($queries as $sql) {
            $conn->executeQuery($sql);
        }
    }

    private function insertData($conn)
    {
        for ($i = 1; $i <= 50; $i++) {
            $conn->insert('posts', array('username' => 'Jon Doe', 'post_content' => 'Post #'.$i));
            for ($j = 1; $j <= 5; $j++) {
                $conn->insert('comments', array('post_id' => $i, 'username' => 'Jon Doe', 'content' => 'Comment #'.$j));
            }
        }
    }
}
