<?php

namespace Pagerfanta\Iterator;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class IteratorIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $mainIterator;

    /**
     * @var \Iterator
     */
    private $currentIterator;

    private $key;

    public function __construct(\Iterator $mainIterator)
    {
        $this->mainIterator = $mainIterator;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->getCurrentIterator()->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->key++;

        $this->getCurrentIterator()->next();

        if (!$this->currentIterator->valid()) {
            $this->mainIterator->next();
            $this->currentIterator = $this->mainIterator->current();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->mainIterator->valid() && $this->getCurrentIterator()->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->mainIterator->rewind();
        $this->currentIterator = null;
        $this->key = 0;
    }

    private function getCurrentIterator()
    {
        if (null === $this->currentIterator) {
            $this->currentIterator = $this->mainIterator->current();
        }

        return $this->currentIterator;
    }
}
