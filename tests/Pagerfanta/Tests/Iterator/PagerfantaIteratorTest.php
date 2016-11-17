<?php

namespace AdrienBrault\PagerfantaIterator\Tests;

use Pagerfanta\Iterator\IteratorIterator;
use Pagerfanta\Iterator\PagerfantaIterator;
use Pagerfanta\Pagerfanta;

class PagerfantaIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $adapterProphecy = $this->prophesize('Pagerfanta\Adapter\AdapterInterface');
        $adapterProphecy->getNbResults()->willReturn(18);
        $adapterProphecy->getSlice(0, 5)->willReturn(range(0, 4))->shouldBeCalledTimes(1);
        $adapterProphecy->getSlice(5, 5)->willReturn(range(5, 9))->shouldBeCalledTimes(1);
        $adapterProphecy->getSlice(10, 5)->willReturn(range(10, 14))->shouldBeCalledTimes(1);
        $adapterProphecy->getSlice(15, 5)->willReturn(range(15, 18))->shouldBeCalledTimes(1);

        $pager = new Pagerfanta($adapterProphecy->reveal());
        $pager->setMaxPerPage(5);

        $pagerIterator = new IteratorIterator(new PagerfantaIterator($pager));
        $this->assertSame(
            range(0, 18),
            iterator_to_array($pagerIterator)
        );
    }
}
