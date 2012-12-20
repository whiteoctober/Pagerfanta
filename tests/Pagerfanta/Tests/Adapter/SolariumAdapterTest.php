<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\SolariumAdapter;

class SolariumAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Solarium\Core\Client\Client')) {
            $this->markTestSkipped('Solarium is not available.');
        }
    }

    public function testGetNbResults()
    {
        $query = $this->getSolariumQueryMock();

        $client = $this->getSolariumClientMock();
        $client
            ->expects($this->once())
            ->method('select')
            ->with($query)
            ->will($this->returnValue($this->getSolariumResultMock()));

        $adapter = new SolariumAdapter($client, $query);

        $adapter->getNbResults();
    }

    public function testGetSlice()
    {
        $query = $this->getSolariumQueryMock();
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

        $client = $this->getSolariumClientMock();
        $client
            ->expects($this->once())
            ->method('select')
            ->with($query)
            ->will($this->returnValue($this->getSolariumResultMock()));

        $adapter = new SolariumAdapter($client, $query);

        $adapter->getSlice(1, 200);
    }

    private function getSolariumClientMock()
    {
        return $this->getMockBuilder('Solarium\Core\Client\Client')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getSolariumQueryMock()
    {
        return $this->getMock('Solarium\QueryType\Select\Query\Query');
    }

    private function getSolariumResultMock()
    {
        return $this->getMockBuilder('Solarium\QueryType\Select\Result\Result')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
