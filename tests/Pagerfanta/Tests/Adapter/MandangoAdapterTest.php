<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\MandangoAdapter;

class MandangoAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $query;
    protected $adapter;

    protected function setUp()
    {
        if (!class_exists('Mandango\Query')) {
            $this->markTestSkipped('Mandango is not available');
        }

        $this->query = $this
            ->getMockBuilder('Mandango\Query')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->adapter = new MandangoAdapter($this->query);
    }

    public function testGetQuery()
    {
        $this->assertSame($this->query, $this->adapter->getQuery());
    }

    public function testGetNbResults()
    {
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
            ->method('skip')
            ->with($offset)
            ->will($this->returnValue($this->query))
        ;
        $this->query
            ->expects($this->once())
            ->method('all')
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
