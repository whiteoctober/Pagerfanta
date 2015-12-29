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

class MappingAdapter implements AdapterInterface
{
    protected $innerAdapter;
    protected $callback;

    /**
     * MappingAdapter constructor.
     * @param AdapterInterface $innerAdapter
     * @param $callback
     */
    public function __construct(AdapterInterface $innerAdapter, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(sprintf('$callable must be callback'));
        }
        
        $this->innerAdapter = $innerAdapter;
        $this->callback = $callback;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        return $this->innerAdapter->getNbResults();
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        $slice = $this->innerAdapter->getSlice($offset, $length);

        $newSlice = array();
        
        foreach ($slice as $index => $item) {
            $newSlice[$index] = call_user_func($this->callback, $item);
        }
        
        return $newSlice;
    }
}