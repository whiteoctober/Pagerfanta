<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pagerfanta;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Exception\LogicException;
use Pagerfanta\Exception\NotIntegerMaxPerPageException;
use Pagerfanta\Exception\LessThan1MaxPerPageException;
use Pagerfanta\Exception\NotIntegerCurrentPageException;
use Pagerfanta\Exception\LessThan1CurrentPageException;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;

/**
 * Pagerfanta.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 *
 * @api
 */
class Pagerfanta implements PagerfantaInterface
{
    private $adapter;
    private $maxPerPage;
    private $currentPage;
    private $currentPageResults;
    private $nbResults;
    private $nbPages;

    /**
     * Constructor.
     *
     * @param AdapterInterface $adapter    An adapter.
     * @param integer          $maxPerPage The maximum of results per page.
     *
     * @api
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->maxPerPage = 10;
        $this->currentPage = 1;
    }

    /**
     * Returns the adapter.
     *
     * @return AdapterInterface The adapter.
     *
     * @api
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxPerPage($maxPerPage)
    {
        // tries to normalize from string to integer
        if (is_string($maxPerPage) && (int) $maxPerPage == $maxPerPage) {
            $maxPerPage = (int) $maxPerPage;
        }

        if (!is_int($maxPerPage)) {
            throw new NotIntegerMaxPerPageException();
        }

        if ($maxPerPage < 1) {
            throw new LessThan1MaxPerPageException();
        }

        $this->currentPageResults = null;
        $this->nbPages = null;
        $this->maxPerPage = $maxPerPage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentPage($currentPage, $allowOutOfRangePages = false, $normalizeOutOfRangePages = false)
    {
        // tries to normalize from string to integer
        if (is_string($currentPage) && (int) $currentPage == $currentPage) {
            $currentPage = (int) $currentPage;
        }

        // integer?
        if (!is_int($currentPage)) {
            throw new NotIntegerCurrentPageException();
        }

        // less than 1?
        if ($currentPage < 1) {
            throw new LessThan1CurrentPageException();
        }

        // out of range pages
        if (!$allowOutOfRangePages) {
            if ($currentPage > $this->getNbPages()) {
                if (!$normalizeOutOfRangePages) {
                    throw new OutOfRangeCurrentPageException();
                }

                $currentPage = $this->getNbPages();
            }
        }

        $this->currentPageResults = null;
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPageResults()
    {
        if (null === $this->currentPageResults) {
            $offset = ($this->getCurrentPage() - 1) * $this->getMaxPerPage();
            $length = $this->getMaxPerPage();
            $this->currentPageResults = $this->adapter->getSlice($offset, $length);
        }

        return $this->currentPageResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        if (null === $this->nbResults) {
            $this->nbAllResults = $this->getAdapter()->getNbResults();
        }

        return $this->nbAllResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbPages()
    {
        if (null === $this->nbPages) {
            $this->nbPages = (int) ceil($this->getNbResults() / $this->getMaxPerPage());
        }

        return $this->nbPages;
    }

    /**
     * {@inheritdoc}
     */
    public function haveToPaginate()
    {
        return $this->getNbResults() > $this->maxPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPreviousPage()
    {
        return $this->currentPage > 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousPage()
    {
        if (!$this->hasPreviousPage()) {
            throw new LogicException('There is not previous page.');
        }

        return $this->currentPage - 1;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNextPage()
    {
        return $this->currentPage < $this->getNbPages();
    }

    /**
     * {@inheritdoc}
     */
    public function getNextPage()
    {
        if (!$this->hasNextPage()) {
            throw new LogicException('There is not next page.');
        }

        return $this->currentPage + 1;
    }

    /**
     * Implements the \IteratorAggregate interface.
     *
     * Returns an \ArrayIterator instance with the current results.
     *
     * @api
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getCurrentPageResults());
    }
}
