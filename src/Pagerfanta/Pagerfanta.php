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
use Pagerfanta\Exception\NotBooleanException;
use Pagerfanta\Exception\NotIntegerMaxPerPageException;
use Pagerfanta\Exception\LessThan1MaxPerPageException;
use Pagerfanta\Exception\NotIntegerCurrentPageException;
use Pagerfanta\Exception\LessThan1CurrentPageException;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;

/**
 * Represents a paginator.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Pagerfanta implements \Countable, \IteratorAggregate, PagerfantaInterface
{
    private $adapter;
    private $allowOutOfRangePages;
    private $normalizeOutOfRangePages;
    private $maxPerPage;
    private $currentPage;
    private $nbResults;
    private $currentPageResults;

    /**
     * @param AdapterInterface $adapter An adapter.
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->allowOutOfRangePages = false;
        $this->normalizeOutOfRangePages = false;
        $this->maxPerPage = 10;
        $this->currentPage = 1;
    }

    /**
     * Returns the adapter.
     *
     * @return AdapterInterface The adapter.
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Sets whether or not allow out of range pages.
     *
     * @param Boolean $value
     */
    public function setAllowOutOfRangePages($value)
    {
        $this->allowOutOfRangePages = $this->filterBoolean($value);

        return $this;
    }

    /**
     * Returns whether or not allow out of range pages.
     *
     * @return Boolean
     */
    public function getAllowOutOfRangePages()
    {
        return $this->allowOutOfRangePages;
    }

    /**
     * Sets whether or not normalize out of range pages.
     *
     * @param Boolean $value
     */
    public function setNormalizeOutOfRangePages($value)
    {
        $this->normalizeOutOfRangePages = $this->filterBoolean($value);

        return $this;
    }

    /**
     * Returns whether or not normalize out of range pages.
     *
     * @return Boolean
     */
    public function getNormalizeOutOfRangePages()
    {
        return $this->normalizeOutOfRangePages;
    }

    private function filterBoolean($value)
    {
        if (!is_bool($value)) {
            throw new NotBooleanException();
        }

        return $value;
    }

    /**
     * Sets the max per page.
     *
     * Tries to convert from string and float.
     *
     * @param integer $maxPerPage
     *
     * @throws NotIntegerMaxPerPageException If the max per page is not an integer even converting.
     * @throws LessThan1MaxPerPageException  If the max per page is less than 1.
     */
    public function setMaxPerPage($maxPerPage)
    {
        $this->maxPerPage = $this->filterMaxPerPage($maxPerPage);
        $this->resetForMaxPerPageChange();

        return $this;
    }

    private function filterMaxPerPage($maxPerPage)
    {
        $maxPerPage = $this->toInteger($maxPerPage);
        $this->checkMaxPerPage($maxPerPage);

        return $maxPerPage;
    }

    private function checkMaxPerPage($maxPerPage)
    {
        if (!is_int($maxPerPage)) {
            throw new NotIntegerMaxPerPageException();
        }

        if ($maxPerPage < 1) {
            throw new LessThan1MaxPerPageException();
        }
    }

    private function resetForMaxPerPageChange()
    {
        $this->currentPageResults = null;
        $this->nbResults = null;
    }

    /**
     * Returns the max per page.
     *
     * @return integer
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }

    /**
     * Sets the current page.
     *
     * Tries to convert from string and float.
     *
     * @param integer $currentPage
     *
     * @throws NotIntegerCurrentPageException If the current page is not an integer even converting.
     * @throws LessThan1CurrentPageException  If the current page is less than 1.
     * @throws OutOfRangeCurrentPageException If It is not allowed out of range pages and they are not normalized.
     */
    public function setCurrentPage($currentPage)
    {
        $this->useDeprecatedCurrentPageBooleanArguments(func_get_args());

        $this->currentPage = $this->filterCurrentPage($currentPage);
        $this->resetForCurrentPageChange();

        return $this;
    }

    private function useDeprecatedCurrentPageBooleanArguments($arguments)
    {
        $this->useDeprecatedCurrentPageAllowOutOfRangePagesBooleanArgument($arguments);
        $this->useDeprecatedCurrentPageNormalizeOutOfRangePagesBooleanArgument($arguments);
    }

    private function useDeprecatedCurrentPageAllowOutOfRangePagesBooleanArgument($arguments)
    {
        $index = 1;
        $method = 'setAllowOutOfRangePages';

        $this->useDeprecatedBooleanArgument($arguments, $index, $method);
    }

    private function useDeprecatedCurrentPageNormalizeOutOfRangePagesBooleanArgument($arguments)
    {
        $index = 2;
        $method = 'setNormalizeOutOfRangePages';

        $this->useDeprecatedBooleanArgument($arguments, $index, $method);
    }

    private function useDeprecatedBooleanArgument($arguments, $index, $method)
    {
        if (isset($arguments[$index])) {
            $this->$method($arguments[$index]);
        }
    }

    private function filterCurrentPage($currentPage)
    {
        $currentPage = $this->toInteger($currentPage);
        $this->checkCurrentPage($currentPage);
        $currentPage = $this->filterOutOfRangeCurrentPage($currentPage);

        return $currentPage;
    }

    private function checkCurrentPage($currentPage)
    {
        if (!is_int($currentPage)) {
            throw new NotIntegerCurrentPageException();
        }

        if ($currentPage < 1) {
            throw new LessThan1CurrentPageException();
        }
    }

    private function filterOutOfRangeCurrentPage($currentPage)
    {
        if ($this->notAllowedCurrentPageOutOfRange($currentPage)) {
            return $this->normalizeOutOfRangeCurrentPage($currentPage);
        }

        return $currentPage;
    }

    private function notAllowedCurrentPageOutOfRange($currentPage)
    {
        return !$this->getAllowOutOfRangePages() &&
               $this->currentPageOutOfRange($currentPage);
    }

    private function currentPageOutOfRange($currentPage)
    {
        return $currentPage > 1 && $currentPage > $this->getNbPages();
    }

    private function normalizeOutOfRangeCurrentPage($currentPage)
    {
        if ($this->getNormalizeOutOfRangePages()) {
            return $this->getNbPages();
        }

        throw new OutOfRangeCurrentPageException(sprintf('Page "%d" does not exist. The currentPage must be inferior to "%d"', $currentPage, $this->getNbPages()));
    }

    private function resetForCurrentPageChange()
    {
        $this->currentPageResults = null;
    }

    /**
     * Returns the current page.
     *
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Returns the results for the current page.
     *
     * @return array|\Traversable
     */
    public function getCurrentPageResults()
    {
        if ($this->notCachedCurrentPageResults()) {
            $this->currentPageResults = $this->getCurrentPageResultsFromAdapter();
        }

        return $this->currentPageResults;
    }

    private function notCachedCurrentPageResults()
    {
        return $this->currentPageResults === null;
    }

    private function getCurrentPageResultsFromAdapter()
    {
        $offset = $this->calculateOffsetForCurrentPageResults();
        $length = $this->getMaxPerPage();

        return $this->adapter->getSlice($offset, $length);
    }

    private function calculateOffsetForCurrentPageResults()
    {
        return ($this->getCurrentPage() - 1) * $this->getMaxPerPage();
    }

    /**
     * Calculates the current page offset start
     *
     * @return int
     */
    public function getCurrentPageOffsetStart()
    {
        return $this->getNbResults() ?
               $this->calculateOffsetForCurrentPageResults() + 1 :
               0;
    }

    /**
     * Calculates the current page offset end
     *
     * @return int
     */
    public function getCurrentPageOffsetEnd()
    {
        return $this->hasNextPage() ?
               $this->getCurrentPage() * $this->getMaxPerPage() :
               $this->getNbResults();
    }

    /**
     * Returns the number of results.
     *
     * @return integer
     */
    public function getNbResults()
    {
        if ($this->notCachedNbResults()) {
            $this->nbResults = $this->getAdapter()->getNbResults();
        }

        return $this->nbResults;
    }

    private function notCachedNbResults()
    {
        return $this->nbResults === null;
    }

    /**
     * Returns the number of pages.
     *
     * @return integer
     */
    public function getNbPages()
    {
        $nbPages = $this->calculateNbPages();

        if ($nbPages == 0) {
            return $this->minimumNbPages();
        }

        return $nbPages;
    }

    private function calculateNbPages()
    {
        return (int) ceil($this->getNbResults() / $this->getMaxPerPage());
    }

    private function minimumNbPages()
    {
        return 1;
    }

    /**
     * Returns if the number of results is higher than the max per page.
     *
     * @return Boolean
     */
    public function haveToPaginate()
    {
        return $this->getNbResults() > $this->maxPerPage;
    }

    /**
     * Returns whether there is previous page or not.
     *
     * @return Boolean
     */
    public function hasPreviousPage()
    {
        return $this->currentPage > 1;
    }

    /**
     * Returns the previous page.
     *
     * @return integer
     *
     * @throws LogicException If there is no previous page.
     */
    public function getPreviousPage()
    {
        if (!$this->hasPreviousPage()) {
            throw new LogicException('There is not previous page.');
        }

        return $this->currentPage - 1;
    }

    /**
     * Returns whether there is next page or not.
     *
     * @return Boolean
     */
    public function hasNextPage()
    {
        return $this->currentPage < $this->getNbPages();
    }

    /**
     * Returns the next page.
     *
     * @return integer
     *
     * @throws LogicException If there is no next page.
     */
    public function getNextPage()
    {
        if (!$this->hasNextPage()) {
            throw new LogicException('There is not next page.');
        }

        return $this->currentPage + 1;
    }

    /**
     * Implements the \Countable interface.
     *
     * Return integer The number of results.
     */
    public function count()
    {
        return $this->getNbResults();
    }

    /**
     * Implements the \IteratorAggregate interface.
     *
     * Returns an \ArrayIterator instance with the current results.
     */
    public function getIterator()
    {
        $results = $this->getCurrentPageResults();

        if ($results instanceof \Iterator) {
            return $results;
        }

        if ($results instanceof \IteratorAggregate) {
            return $results->getIterator();
        }

        return new \ArrayIterator($results);
    }

    private function toInteger($value)
    {
        if ($this->needsToIntegerConvertion($value)) {
            return (int) $value;
        }

        return $value;
    }

    private function needsToIntegerConvertion($value)
    {
        return (is_string($value) || is_float($value)) && (int) $value == $value;
    }
}
