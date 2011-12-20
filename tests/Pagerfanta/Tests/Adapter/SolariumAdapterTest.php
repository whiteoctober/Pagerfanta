<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\SolariumAdapter;

class SolariumAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Solarium_Client')) {
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
        return $this->getMock('Solarium_Client');
    }

    private function getSolariumQueryMock()
    {
        return $this->getMock('Solarium_Query_Select');
    }

    private function getSolariumResultMock()
    {
        return $this->getMockBuilder('Solarium_Result_Select')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
