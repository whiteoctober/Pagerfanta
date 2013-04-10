<?php

namespace Pagerfanta\Tests\Adapter;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Schema;
use Pagerfanta\Adapter\DoctrineDBALSingleTableAdapter;

class DoctrineDbalSingleTableAdapterTestt extends DoctrineDbalTestCase
{
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $this->adapter = new DoctrineDbalSingleTableAdapter($this->q, 'p.id');
    }

    public function testGetNbResults()
    {
        $this->doTestGetNbResults();
    }

    public function testGetNbResultsShouldWorkAfterCallingGetSlice()
    {
        $this->adapter->getSlice(1, 10);

        $this->doTestGetNbResults();
    }

    private function doTestGetNbResults()
    {
        $this->assertSame(50, $this->adapter->getNbResults());
    }

    public function testGetNbResultWithNoData()
    {
        $q = clone $this->q;
        $q->delete('posts')->execute();

        $this->assertSame(0, $this->adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $this->doTestGetSlice();
    }

    public function testGetSliceShouldWorkAfterCallingGetNbResults()
    {
        $this->adapter->getNbResults();

        $this->doTestGetSlice();
    }

    private function doTestGetSlice()
    {
        $offset = 30;
        $length = 10;

        $q = clone $this->q;
        $q->setFirstResult($offset)->setMaxResults($length);
        $expectedResults = $q->execute()->fetchAll();

        $results = $this->adapter->getSlice($offset, $length);
        $this->assertSame($expectedResults, $results);
    }

    /**
     * @expectedException Pagerfanta\Exception\InvalidArgumentException
     */
    public function testItShouldThrowAnInvalidArgumentExceptionIfTheCountFieldDoesNotHaveAlias()
    {
        new DoctrineDBALSingleTableAdapter($this->q, 'id');
    }
}
