<?php

namespace Pagerfanta\Iterator;

use Pagerfanta\Pagerfanta;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class PagerfantaIterator implements \Iterator
{
    /**
     * @var Pagerfanta
     */
    private $pager;

    private $currentPage;

    public function __construct(Pagerfanta $pager)
    {
        $this->pager = clone $pager;
        $this->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->pager->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->currentPage++;

        if ($this->valid()) {
            $this->pager->setCurrentPage($this->pager->getNextPage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->pager->getCurrentPage();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->currentPage <= $this->pager->getNbPages();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->currentPage = 1;
        $this->pager->setCurrentPage($this->currentPage);
    }

    public static function iterateOverPages(Pagerfanta $pager)
    {
        return new static($pager);
    }

    public static function iterateOverElements(Pagerfanta $pager)
    {
        return new IteratorIterator(static::iterateOverPages($pager));
    }
}
