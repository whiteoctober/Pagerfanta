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
     * @param callable $callback
     */
    public function __construct(AdapterInterface $innerAdapter, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callable must be callback');
        }
        
        $this->innerAdapter = $innerAdapter;
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return $this->innerAdapter->getNbResults();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return call_user_func($this->callback, $this->innerAdapter->getSlice($offset, $length));
    }
}