<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\SolariumAdapter;

abstract class SolariumAdapterTest extends \PHPUnit_Framework_TestCase
{
    abstract protected function getSolariumName();

    abstract protected function getClientClass();
    abstract protected function getQueryClass();
    abstract protected function getResultClass();

    public function setUp()
    {
        if ($this->isSolariumNotAvailable()) {
            $this->markTestSkipped($this->getSolariumName().' is not available.');
        }
    }

    private function isSolariumNotAvailable()
    {
        return !class_exists($this->getClientClass());
    }

    /**
     * @expectedException Pagerfanta\Exception\InvalidArgumentException
     */
    public function testConstructorShouldThrowAnInvalidArgumentExceptionWhenInvalidClient()
    {
        new SolariumAdapter(new \ArrayObject(), $this->createQueryMock());
    }

    /**
     * @expectedException Pagerfanta\Exception\InvalidArgumentException
     */
    public function testConstructorShouldThrowAnInvalidArgumentExceptionWhenInvalidQuery()
    {
        new SolariumAdapter($this->createClientMock(), new \ArrayObject());
    }

    public function testGetNbResults()
    {
        $query = $this->createQueryMock();

        $result = $this->createResultMock();
        $result
            ->expects($this->once())
            ->method('getNumFound')
            ->will($this->returnValue(100));

        $client = $this->createClientMock();
        $client
            ->expects($this->once())
            ->method('select')
            ->with($query)
            ->will($this->returnValue($result));

        $adapter = new SolariumAdapter($client, $query);

        $this->assertSame(100, $adapter->getNbResults());
    }

    public function testGetNbResultsCanUseACachedTheResultSet()
    {
        $query = $this->createQueryStub();

        $client = $this->createClientMock();
        $client
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->createResultMock()));

        $adapter = new SolariumAdapter($client, $query);

        $adapter->getSlice(1, 1);
        $adapter->getNbResults();
    }

    public function testGetSlice()
    {
        $query = $this->createQueryMock();
        $query
            ->expects($this->any())
            ->method('setStart')
            ->with(1)
            ->will($this->returnValue($query));
        $query
            ->expects($this->any())
            ->method('setRows')
            ->with(200)
            ->will($this->returnValue($query));

        $result = $this->createResultMock();

        $client = $this->createClientMock();
        $client
            ->expects($this->once())
            ->method('select')
            ->with($query)
            ->will($this->returnValue($result));

        $adapter = new SolariumAdapter($client, $query);

        $this->assertSame($result, $adapter->getSlice(1, 200));
    }

    public function testGetSliceCannotUseACachedResultSet()
    {
        $query = $this->createQueryStub();

        $client = $this->createClientMock();
        $client
            ->expects($this->exactly(2))
            ->method('select')
            ->will($this->returnValue($this->createResultMock()));

        $adapter = new SolariumAdapter($client, $query);

        $adapter->getNbResults();
        $adapter->getSlice(1, 200);
    }

    public function testGetResultSet()
    {
        $query = $this->createQueryMock();
        $client = $this->createClientMock();
        $client
            ->expects($this->atLeastOnce())
            ->method('select')
            ->will($this->returnValue($this->createResultMock()));

        $adapter = new SolariumAdapter($client, $query);
        $this->assertInstanceOf($this->getResultClass(), $adapter->getResultSet());
    }

    private function createClientMock()
    {
        return $this->getMockBuilder($this->getClientClass())
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function createQueryMock()
    {
        return $this->getMock($this->getQueryClass());
    }

    private function createQueryStub()
    {
        $query = $this->createQueryMock();
        $query
            ->expects($this->any())
            ->method('setStart')
            ->will($this->returnSelf());
        $query
            ->expects($this->any())
            ->method('setRows')
            ->will($this->returnSelf());

        return $query;
    }

    private function createResultMock()
    {
        return $this->getMockBuilder($this->getResultClass())
            ->disableOriginalConstructor()
            ->getMock();
    }
}
