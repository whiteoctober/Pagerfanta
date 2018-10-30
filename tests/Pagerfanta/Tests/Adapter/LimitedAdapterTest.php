<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\LimitedAdapter;
use PHPUnit\Framework\TestCase;

class LimitedAdapterTest extends TestCase
{
    /**
     * @dataProvider nbResultsProvider
     */
    public function testNbResults(int $nbResultsFromAdapter, int $maxNbResults, int $result)
    {
        $decorated = $this->createMock(AdapterInterface::class);

        $decorated
            ->expects($this->once())
            ->method('getNbResults')
            ->will($this->returnValue($nbResultsFromAdapter)));

        $adapter = new LimitedAdapter($decorated, $maxNbResults);
        $this->assertSame($result, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $decorated = $this->createMock(AdapterInterface::class);
        $offset = 0;
        $limit = 5;
        $result = [0, 1, 2, 3, 4];

        $decorated
            ->expects($this->once())
            ->method('getSlice')
            ->with($offset, $limit)
            ->will($this->returnValue($result));

        $adapter = new LimitedAdapter($decorated, 100);
        $this->assertSame($result, $adapter->getSlice($offset, $limit));
    }

    public function nbResultsProvider()
    {
        yield 'Adapter returns more results than allowed.' => [100, 90, 90];

        yield 'Adapter returns less results than allowed.' => [100, 1000, 100];
    }
}
