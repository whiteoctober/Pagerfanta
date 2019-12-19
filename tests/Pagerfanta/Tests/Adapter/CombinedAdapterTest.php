<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\CombinedAdapter;
use PHPUnit\Framework\TestCase;

class CombinedAdapterTest extends TestCase
{
    /** @var ArrayAdapter[] */
    private static $adapters = [];

    protected function setUp()
    {
        self::$adapters = [
            new ArrayAdapter(range(1, 88)),
            new ArrayAdapter(range(10001, 10105)),
            new ArrayAdapter(range(1000001, 1000105)),
        ];
    }

    public function testNbResults()
    {
        $multiAdapter = new CombinedAdapter(self::$adapters);

        $total = 0;
        foreach (self::$adapters as $adapter) {
            $total += $adapter->getNbResults();
        }

        $this->assertSame($total, $multiAdapter->getNbResults());
    }

    public function testSlices()
    {
        $multiAdapter = new CombinedAdapter(self::$adapters);

        $this->assertSame(
            self::$adapters,
            $multiAdapter->getAdapters()
        );

        $this->assertSame(
            self::$adapters[0]->getSlice(0, 50),
            iterator_to_array($multiAdapter->getSlice(0, 50))
        );

        $this->assertSame(
            self::$adapters[0]->getSlice(0, 88),
            iterator_to_array($multiAdapter->getSlice(0, 88))
        );

        $this->assertSame(
            array_merge(
                self::$adapters[0]->getSlice(50, 38),
                self::$adapters[1]->getSlice(0, 12)
            ),
            iterator_to_array($multiAdapter->getSlice(50, 50))
        );

        $this->assertSame(
            self::$adapters[1]->getSlice(12, 50),
            iterator_to_array($multiAdapter->getSlice(100, 50))
        );

        $this->assertSame(
            array_merge(
                self::$adapters[1]->getSlice(62, 43),
                self::$adapters[2]->getSlice(0, 7)
            ),
            iterator_to_array($multiAdapter->getSlice(150, 50))
        );

        $this->assertSame(
            array_merge(
                self::$adapters[0]->getArray(),
                self::$adapters[1]->getArray(),
                self::$adapters[2]->getArray()
            ),
            iterator_to_array($multiAdapter->getSlice(0, 300))
        );
    }
}
