<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\FixedAdapter;

class FixedAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNbResults()
    {
        $adapter = new FixedAdapter(array(), 5);
        $this->assertSame(5, $adapter->getNbResults());
    }

    public function testGetResults()
    {
        $data = array('a', 'b');
        $adapter = new FixedAdapter($data, 5);
        $this->assertSame($data, $adapter->getSlice(0, 10));
        $this->assertSame($data, $adapter->getSlice(10, 20));

        $data = new \stdClass;
        $adapter = new FixedAdapter($data, 5);
        $this->assertSame($data, $adapter->getSlice(0, 10));
        $this->assertSame($data, $adapter->getSlice(10, 20));
    }
}
