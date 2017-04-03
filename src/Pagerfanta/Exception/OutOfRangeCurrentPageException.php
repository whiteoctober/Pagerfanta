<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pagerfanta\Exception;

/**
 * OutOfRangeCurrentPageException.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class OutOfRangeCurrentPageException extends NotValidCurrentPageException
{
    private $currentPage;
    private $maxAllowedPage;

    /**
     * @return null|integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param integer $page
     */
    public function setCurrentPage($page)
    {
        $this->currentPage = $page;
    }

    /**
     * @return null|integer
     */
    public function getMaxAllowedPage()
    {
        return $this->maxAllowedPage;
    }

    /**
     * @param integer $page
     */
    public function setMaxAllowedPage($page)
    {
        $this->maxAllowedPage = $page;
    }
}
