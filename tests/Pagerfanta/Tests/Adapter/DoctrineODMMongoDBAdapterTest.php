<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;

class DoctrineODMMongoDBAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $queryBuilder;
    protected $query;
    protected $adapter;

    protected function setUp()
    {
        if (!class_exists('Doctrine\ODM\MongoDB\Query\Builder')) {
            $this->markTestSkipped('Doctrine MongoDB is not available');
        }

        $this->queryBuilder = $this
            ->getMockBuilder('Doctrine\ODM\MongoDB\Query\Builder')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->query = $this
            ->getMockBuilder('Doctrine\ODM\MongoDB\Query\Query')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->adapter = new DoctrineODMMongoDBAdapter($this->queryBuilder);
    }

    public function testGetQueryBuilder()
    {
        $this->assertSame($this->queryBuilder, $this->adapter->getQueryBuilder());
    }

    public function testGetNbResults()
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($this->query))
        ;
        $this->query
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(110))
        ;

        $this->assertSame(110, $this->adapter->getNbResults());
    }

    /**
     * @dataProvider getResultsProvider
     */
    public function testGetResults($offset, $length)
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('limit')
            ->with($length)
            ->will($this->returnValue($this->queryBuilder))
        ;
        $this->queryBuilder
            ->expects($this->once())
            ->method('skip')
            ->with($offset)
            ->will($this->returnValue($this->queryBuilder))
        ;
        $this->queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($this->query))
        ;
        $this->query
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(new \ArrayIterator($all = array(new \DateTime(), new \DateTime()))))
        ;

        $this->assertSame($all, $this->adapter->getSlice($offset, $length));
    }

    public function getResultsProvider()
    {
        return array(
            array(2, 10),
            array(3, 2),
        );
    }
}
