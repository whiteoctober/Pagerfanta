<?php

namespace AdrienBrault\PagerfantaIterator\Tests;

use Pagerfanta\Iterator\IteratorIterator;

class IteratorIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $iteratorIterator = new IteratorIterator(new \ArrayIterator(array(
            new \ArrayIterator(array('hello', 'world')),
            new \ArrayIterator(array('wow', 'such', 'php')),
        )));
        $this->assertSame(
            array(
                'hello',
                'world',
                'wow',
                'such',
                'php',
            ),
            iterator_to_array($iteratorIterator)
        );
    }
}
