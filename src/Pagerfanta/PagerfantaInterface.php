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
interface PagerfantaInterface
{
    /**
     * Sets the max per page.
     *
     * @param integer $maxPerPage The max per page.
     *
     * @api
     */
    function setMaxPerPage($maxPerPage);

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
    function getMaxPerPage();

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
    function setCurrentPage($currentPage);

    /**
     * Returns the current page.
     *
     * @return integer The current page.
     *
     * @api
     */
    function getCurrentPage();

    /**
     * Returns the results for the current page.
     *
     * @return array The results.
     *
     * @api
     */
    function getCurrentPageResults();

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     *
     * @api
     */
    function getNbResults();

    /**
     * Returns the number of pages.
     *
     * @return integer The number of pages.
     *
     * @api
     */
    function getNbPages();

    /**
     * Returns the lower offset of resultset
     *
     * @return integer The lower offset
     */
    function getLowerOffsetOfResults();

    /**
     * Returns the higher offset of resultset
     *
     * @return integer The lower offset
     */
    function getHigherOffsetOfResults();

    /**
     * Returns whether have to paginate or not.
     *
     * This is true if the number of results is higher than the max per page.
     *
     * @return Boolean Whether have to paginate or not.
     */
    function haveToPaginate();

    /**
     * Returns whether there is previous page or not.
     *
     * @return Boolean Whether there is previous page or not.
     *
     * @api
     */
    function hasPreviousPage();

    /**
     * Returns the previous page.
     *
     * @return integer The previous page.
     *
     * @throws PagerfantaException If there is not previous page.
     *
     * @api
     */
    function getPreviousPage();

    /**
     * Returns whether there is next page or not.
     *
     * @return Boolean Whether there is previous page or not.
     *
     * @api
     */
    function hasNextPage();

    /**
     * Returns the next page.
     *
     * @return integer The next page.
     *
     * @throws PagerfantaException If there is not next page.
     *
     * @api
     */
    function getNextPage();
}
