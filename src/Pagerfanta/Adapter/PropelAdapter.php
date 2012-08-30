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
 * PropelAdapter.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class PropelAdapter implements AdapterInterface
{
    private $query;
    private $maxResults;

    /**
     * Constructor.
     */
    public function __construct($query)
    {
        $this->query = $query;
        $this->maxResults = 0;
    }

    /**
     * Returns the query.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $q = clone $this->getQuery();

        return $q->limit($this->getMaxResults())->offset(0)->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $q = clone $this->getQuery();

        return $q->limit($length)->offset($offset)->find();
    }

    /**
     * This method implements a fluent interface.
     *
     * {@inheritdoc}
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }
}
