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

/**
 * AdapterInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 *
 * @api
 */
interface AdapterInterface
{
    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     *
     * @api
     */
    function getNbResults();

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     *
     * @api
     */
    function getSlice($offset, $length);

    /**
     * Set a maximum number of results
     *
     * @param integer $maxResults The max numbers of results.
     *
     * @api
     */
    function setMaxResults($maxResults);

    /**
     * Returns the max results.
     *
     * Tries to normalize from string to integer.
     *
     * @return integer The max results.
     *
     * @api
     */
    function getMaxResults();
}
