<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\ElasticaSearchAdapter;

class ElasticaSearchAdapterTest extends \PHPUnit_Framework_TestCase
{
    private $adapter;
    private $search;

    protected function setUp()
    {
        $this->search = $this->getMockBuilder('Elastica\\Search')->disableOriginalConstructor()->getMock();
        $this->adapter = new ElasticaSearchAdapter($this->search);
    }

    public function testGetNbResults()
    {
        $this->shouldCallOnce('count');
        $this->adapter->getNbResults();
    }

    public function testGetSlice()
    {
        $this->shouldCallOnce('search')->with('', array('from' => 10, 'size' => 30));
        $this->adapter->getSlice(10, 30);
    }

    private function shouldCallOnce($expectedMethod)
    {
        return $this->search->expects($this->once())
            ->method($expectedMethod);
    }
}
