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

/**
 * PagerfantaInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 *
 * @api
 */
interface PagerfantaInterface extends \Countable, \IteratorAggregate
{
    /**
     * Set a maximum number of results
     *
     * @param integer $maxResults The max numbers of results.
     *
     * @api
     */
    public function setMaxResults($maxResults);

    /**
     * Returns the max results.
     *
     * Tries to normalize from string to integer.
     *
     * @return integer The max results.
     *
     * @api
     */
    public function getMaxResults();

    /**
     * Sets the max per page.
     *
     * @param integer $maxPerPage The max per page.
     *
     * @api
     */
    public function setMaxPerPage($maxPerPage);

    /**
     * Returns the max per page.
     *
     * Tries to normalize from string to integer.
     *
     * @return integer The max per page.
     *
     * @throws NotIntegerMaxPerPageException If the max per page is not an integer even normalizing.
     * @throws LessThan1MaxPerPageException  If the max per page is less than 1.
     *
     * @api
     */
    public function getMaxPerPage();

    /**
     * Sets the current page.
     *
     * @param integer $currentPage              The current page.
     * @param Boolean $allowOutOfRangePages     Whether to allow out of range pages or not (false by default).
     * @param Boolean $normalizeOutOfRangePages Whether to show the last page instead (false by default).
     *
     * @throws NotIntegerCurrentPageException If the current page is not an integer even normalizing.
     * @throws LessThan1CurrentPageException  If the current page is less than 1.
     * @throws OutOfRangeCurrentPageException If It is not allowed out of range pages and they are not normalized.
     *
     * @api
     */
    public function setCurrentPage($currentPage);

    /**
     * Returns the current page.
     *
     * @return integer The current page.
     *
     * @api
     */
    public function getCurrentPage();

    /**
     * Returns the results for the current page.
     *
     * @return array|\Traversable The results.
     *
     * @api
     */
    public function getCurrentPageResults();

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     *
     * @api
     */
    public function getNbResults();

    /**
     * Returns the number of pages.
     *
     * @return integer The number of pages.
     *
     * @api
     */
    public function getNbPages();

    /**
     * Returns whether have to paginate or not.
     *
     * This is true if the number of results is higher than the max per page.
     *
     * @return Boolean Whether have to paginate or not.
     */
    public function haveToPaginate();

    /**
     * Returns whether there is previous page or not.
     *
     * @return Boolean Whether there is previous page or not.
     *
     * @api
     */
    public function hasPreviousPage();

    /**
     * Returns the previous page.
     *
     * @return integer The previous page.
     *
     * @throws PagerfantaException If there is not previous page.
     *
     * @api
     */
    public function getPreviousPage();

    /**
     * Returns whether there is next page or not.
     *
     * @return Boolean Whether there is previous page or not.
     *
     * @api
     */
    public function hasNextPage();

    /**
     * Returns the next page.
     *
     * @return integer The next page.
     *
     * @throws PagerfantaException If there is not next page.
     *
     * @api
     */
    public function getNextPage();
}
