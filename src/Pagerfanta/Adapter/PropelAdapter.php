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

    /**
     * Constructor.
     */
    public function __construct($query)
    {
        $this->query = $query;
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
        return $q->limit(0)->offset(0)->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $q = clone $this->getQuery();
        return $q->limit($length)->offset($offset)->find();
    }
}
