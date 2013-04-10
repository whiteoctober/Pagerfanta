<?php

namespace Pagerfanta\Tests\Adapter;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Schema;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class DoctrineDbalAdapterTest extends DoctrineDbalTestCase
{
    public function testGetNbResults()
    {
        $adapter = $this->createAdapterToTestGetNbResults();

        $this->doTestGetNbResults($adapter);
    }

    public function testGetNbResultsShouldWorkAfterCallingGetSlice()
    {
        $adapter = $this->createAdapterToTestGetNbResults();

        $adapter->getSlice(1, 10);

        $this->doTestGetNbResults($adapter);
    }

    private function doTestGetNbResults($adapter)
    {
        $this->assertSame(50, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $adapter = $this->createAdapterToTestGetSlice();

        $this->doTestGetSlice($adapter);
    }

    public function testGetSliceShouldWorkAfterCallingGetNbResults()
    {
        $adapter = $this->createAdapterToTestGetSlice();

        $adapter->getNbResults();

        $this->doTestGetSlice($adapter);
    }

    private function createAdapterToTestGetSlice()
    {
        $countQueryModifier = function () { };

        return new DoctrineDbalAdapter($this->q, $countQueryModifier);
    }

    private function doTestGetSlice($adapter)
    {
        $offset = 30;
        $length = 10;

        $qb = clone $this->q;
        $qb->setFirstResult($offset)->setMaxResults($length);

        $expectedResults = $qb->execute()->fetchAll();
        $results = $adapter->getSlice($offset, $length);

        $this->assertSame($expectedResults, $results);
    }

    /**
     * @expectedException Pagerfanta\Exception\InvalidArgumentException
     */
    public function testItShouldThrowAnInvalidArgumentExceptionIfTheQueryIsNotSelect()
    {
        $this->q->delete('posts');
        $countQueryModifier = function () { };

        new DoctrineDbalAdapter($this->q, $countQueryModifier);
    }

    public function testItShouldCloneTheQuery()
    {
        $adapter = $this->createAdapterToTestGetNbResults();

        $this->q->innerJoin('p', 'comments', 'c', 'c.post_id = p.id')
                ->groupBy('c.post_id');

        $this->assertSame(50, $adapter->getNbResults());
    }

    /**
     * @expectedException Pagerfanta\Exception\InvalidArgumentException
     */
    public function testItShouldThrowAnInvalidArgumentExceptionIfTheCountQueryModifierIsNotACallable()
    {
        $countQueryModifier = 'ups';

        new DoctrineDbalAdapter($this->q, $countQueryModifier);
    }

    private function createAdapterToTestGetNbResults()
    {
        $countQueryModifier = function ($query) {
            $query->select('COUNT(DISTINCT p.id) AS total_results')
                  ->setMaxResults(1);
        };

        return new DoctrineDbalAdapter($this->q, $countQueryModifier);
    }
}
