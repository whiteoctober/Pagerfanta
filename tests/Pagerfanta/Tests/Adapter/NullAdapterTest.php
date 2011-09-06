<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\NullAdapter;

class NullAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $nbResults;
    protected $adapter;

    protected function setUp()
    {
        $this->nbResults = 33;
        $this->adapter = new NullAdapter($this->nbResults);
    }

    public function testGetNbResults()
    {
        $this->assertSame(33, $this->adapter->getNbResults());
    }

    public function testGetResults()
    {
        $this->assertSame(array_fill(0, 10, null), $this->adapter->getSlice(20, 10));
    }

    public function testGetResultsWithRemainCountLessThanLength()
    {
        $this->assertSame(array_fill(0, 3, null), $this->adapter->getSlice(30, 10));
    }

}
