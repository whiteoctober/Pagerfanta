<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\CallbackAdapter;

class FunctionalAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNbResult()
    {
        $returnValue = 42;
        $nbResults = function () use ($returnValue) {
            return $returnValue;
        };
        $adapter = new CallbackAdapter($nbResults, function () {});

        $this->assertEquals($returnValue, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $offset = 42;
        $length = 42 * 2;
        $returnValue = array('foo');
        $self = $this;
        $slice = function ($offset, $length) use ($self, $returnValue, $offset, $length) {
            $self->assertEquals($offset, $offset);
            $self->assertEquals($length, $length);

            return $returnValue;
        };
        $adapter = new CallbackAdapter(function () {}, $slice);

        $this->assertEquals($returnValue, $adapter->getSlice($offset, $length));
    }
}
