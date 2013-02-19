<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\SolariumAdapter;

class SolariumAdapterTest extends \PHPUnit_Framework_TestCase
{
    static private $clientClasses = array(
        'Solarium_Client',
        'Solarium\Client'
    );

    static private $queryClasses = array(
        'Solarium_Query_Select',
        'Solarium\QueryType\Select\Query\Query'
    );

    static private $resultClasses = array(
        'Solarium_Result_Select',
        'Solarium\QueryType\Select\Result\Result'
    );

    static private $clientClass;
    static private $queryClass;
    static private $resultClass;

    public function setUp()
    {
        if ($this->classesNotInitialized()) {
            $this->initializeClasses();
        }

        if (!$this->isSolariumAvailable()) {
            $this->markTestSkipped('Solarium is not available.');
        }
    }

    private function classesNotInitialized()
    {
        return static::$clientClass === null;
    }

    private function initializeClasses()
    {
        foreach (array(
            'client' => static::$clientClasses,
            'query'  => static::$queryClasses,
            'result' => static::$resultClasses
        ) as $name => $classes) {
            $this->initializeClass($name, $classes);
        }
    }

    private function initializeClass($name, $classes)
    {
        static::${$name.'Class'} = $this->find('class_exists', $classes);
    }

    private function find($callback, $collection)
    {
        foreach ($collection as $value) {
            if (call_user_func($callback, $value)) {
                return $value;
            }
        }
    }

    private function isSolariumAvailable()
    {
        return (Boolean) static::$clientClass;
    }

    public function testGetNbResults()
    {
        $query = $this->getQueryMock();

        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('select')
            ->with($query)
            ->will($this->returnValue($this->getResultMock()));

        $adapter = new SolariumAdapter($client, $query);

        $adapter->getNbResults();
    }

    public function testGetSlice()
    {
        $query = $this->getQueryMock();
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

        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('select')
            ->with($query)
            ->will($this->returnValue($this->getResultMock()));

        $adapter = new SolariumAdapter($client, $query);

        $adapter->getSlice(1, 200);
    }

    public function testGetSliceShouldCacheResult()
    {
        $query = $this->getQueryStub();

        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->getResultMock()));

        $adapter = new SolariumAdapter($client, $query);

        $adapter->getSlice(1, 200);
        $adapter->getNbResults();
    }

    public function testGetNbResultsShouldNotCacheResult()
    {
        $query = $this->getQueryStub();

        $client = $this->getClientMock();
        $client
            ->expects($this->exactly(2))
            ->method('select')
            ->will($this->returnValue($this->getResultMock()));

        $adapter = new SolariumAdapter($client, $query);

        $adapter->getNbResults();
        $adapter->getSlice(1, 200);
    }

    private function getClientMock()
    {
        return $this->getMockBuilder(static::$clientClass)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getQueryMock()
    {
        return $this->getMock(static::$queryClass);
    }

    private function getQueryStub()
    {
        $query = $this->getQueryMock();
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

    private function getResultMock()
    {
        return $this->getMockBuilder(static::$resultClass)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
