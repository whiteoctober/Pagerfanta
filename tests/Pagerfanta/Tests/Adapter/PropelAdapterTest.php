<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\PropelAdapter;

/**
 * PropelAdapterTest
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class PropelAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $query;
    protected $adapter;

    protected function setUp()
    {
        if (!class_exists('ModelCriteria')) {
            $this->markTestSkipped('Propel is not available');
        }

        $this->query = $this
            ->getMockBuilder('\ModelCriteria')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->adapter = new PropelAdapter($this->query);
    }

    public function testGetQuery()
    {
        $this->assertSame($this->query, $this->adapter->getQuery());
    }

    public function testGetNbResults()
    {
        $this->query
            ->expects($this->once())
            ->method('limit')
            ->with(0)
            ->will($this->returnValue($this->query))
        ;
        $this->query
            ->expects($this->once())
            ->method('offset')
            ->with(0)
            ->will($this->returnValue($this->query))
        ;
        $this->query
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(100))
        ;

        $this->assertSame(100, $this->adapter->getNbResults());
    }

    /**
     * @dataProvider getResultsProvider
     */
    public function testGetResults($offset, $length)
    {
        $this->query
            ->expects($this->once())
            ->method('limit')
            ->with($length)
            ->will($this->returnValue($this->query))
        ;
        $this->query
            ->expects($this->once())
            ->method('offset')
            ->with($offset)
            ->will($this->returnValue($this->query))
        ;
        $this->query
            ->expects($this->once())
            ->method('find')
            ->will($this->returnValue($all = array(new \DateTime(), new \DateTime())))
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

