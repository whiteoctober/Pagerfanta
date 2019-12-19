<?php

namespace Pagerfanta\Adapter;

/**
 * CombinedAdapter
 *
 * @author Mike Meier <mike@gotom.io>
 */
class CombinedAdapter implements AdapterInterface
{
    /**
     * @var AdapterInterface[]
     */
    private $adapters;

    /**
     * @var array<string, int>
     */
    private $counts = [];

    /**
     * @var int
     */
    private $total = 0;

    /**
     * @var bool
     */
    private $init = false;

    /**
     * Constructor.
     *
     * @param AdapterInterface[] $adapters The adapters to combine.
     */
    public function __construct(array $adapters)
    {
        $this->adapters = $adapters;
    }

    /**
     * Returns the combined adapters.
     *
     * @return AdapterInterface[] The combined Adapters.
     */
    public function getAdapters(): array
    {
        return $this->adapters;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults(): int
    {
        $this->initCounts();

        return $this->total;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length): \Traversable
    {
        $this->initCounts();

        $currentNeeded = $length;
        $currentOffset = $offset;

        foreach ($this->adapters as $adapterKey => $adapter) {
            if ($currentNeeded <= 0) {
                break;
            }

            $adapterCount = $this->counts[spl_object_hash($adapter)];
            $adapterCanProvide = $adapterCount - $currentOffset;

            if ($adapterCanProvide < 0) {
                $currentOffset -= $adapterCount;
                continue;
            }

            $adapterLength = min($adapterCanProvide, $currentNeeded);
            foreach ($adapter->getSlice($currentOffset, $adapterLength) as $value) {
                yield $value;
            }

            $currentNeeded -= $adapterLength;
            $currentOffset = 0;
        }
    }

    private function initCounts()
    {
        if ($this->init) {
            return;
        }

        foreach ($this->adapters as $adapter) {
            $count = $adapter->getNbResults();
            $this->counts[spl_object_hash($adapter)] = $count;
            $this->total += $count;
        }

        $this->init = true;
    }
}
