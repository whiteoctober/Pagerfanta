<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\CallbackAdapter;
use Pagerfanta\Adapter\ConcatenationAdapter;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Adapter\NullAdapter;
use PHPUnit\Framework\TestCase;

class ConcatenationAdapterTest extends TestCase
{
    public function testConstructor()
    {
        new ConcatenationAdapter(array(
            new ArrayAdapter(array()),
            new NullAdapter(),
            new FixedAdapter(0, array())
        ));

        $this->expectException(
            '\Pagerfanta\Exception\InvalidArgumentException'
        );
        new ConcatenationAdapter(array(
            new ArrayAdapter(array()),
            'foo'
        ));
    }

    public function testGetNbResults()
    {
        $adapter = new ConcatenationAdapter(array(
            new ArrayAdapter(array('foo', 'bar', 'baz'))
        ));
        $this->assertEquals(3, $adapter->getNbResults());

        $adapter = new ConcatenationAdapter(array(
            new ArrayAdapter(array_fill(0, 4, 'foo')),
            new ArrayAdapter(array_fill(0, 6, 'bar')),
            new ArrayAdapter(array('baq'))
        ));
        $this->assertEquals(11, $adapter->getNbResults());

        $adapter = new ConcatenationAdapter(array());
        $this->assertEquals(0, $adapter->getNbResults());
    }

    public function testGetResults()
    {
        $adapter = new ConcatenationAdapter(array(
            new ArrayAdapter(array(1, 2, 3, 4, 5, 6)),
            new ArrayAdapter(array(7, 8, 9, 10, 11, 12, 13, 14)),
            new ArrayAdapter(array(15, 16, 17))
        ));
        $this->assertEquals(array(8, 9, 10), $adapter->getSlice(7, 3));
        $this->assertEquals(array(5, 6, 7, 8), $adapter->getSlice(4, 4));
        $this->assertEquals(array(6, 7, 8, 9, 10, 11, 12, 13, 14, 15), $adapter->getSlice(5, 10));
        $this->assertEquals(array(16, 17), $adapter->getSlice(15, 5));
    }

    public function testWithTraversableAdapter()
    {
        $adapter = new ConcatenationAdapter(array(
            new CallbackAdapter(
                function () {
                    return 5;
                },
                function ($offset, $length) {
                    return new \ArrayIterator(array_slice(array(1, 2, 3, 4, 5), $offset, $length));
                }
            ),
            new CallbackAdapter(
                function () {
                    return 3;
                },
                function ($offset, $length) {
                    return new \ArrayIterator(array_slice(array(6, 7, 8), $offset, $length));
                }
            )
        ));
        $this->assertEquals(array(2, 3), $adapter->getSlice(1, 2));
        $this->assertEquals(array(4, 5, 6), $adapter->getSlice(3, 3));
    }
}
