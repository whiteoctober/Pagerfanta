<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pagerfanta\Adapter;

class AdaptersAdapter implements AdapterInterface
{
    /**
     * @var AdapterInterface[]
     */
    private $adapters;

    /**
     * @param AdapterInterface[]  $adapters
     */
    public function __construct(array $adapters)
    {
        $this->adapters = $adapters;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return array_reduce($this->adapters, function ($sum, AdapterInterface $adapter) {
            return $sum + $adapter->getNbResults();
        }, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $slice = array();
        
        foreach ($this->adapters as $adapter) {
            if ($length <= 0) {
                break;
            }
            
            $nbResults = $adapter->getNbResults();

            if ($offset >= $nbResults) {
                $offset -= $nbResults;
                continue;
            }

            foreach ($adapter->getSlice($offset, $length) as $item) {
                $slice[] = $item;
            }
            
            $length += $offset;
            $offset = 0;
            $length -= $nbResults;
        }
        
        return $slice;
    }
}