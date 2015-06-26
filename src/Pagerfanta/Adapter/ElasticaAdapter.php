<?php

/**
 * This file is part of the Pagerfanta project.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pagerfanta\Adapter;

use Elastica\Query;
use Elastica\SearchableInterface;

class ElasticaAdapter implements LazyAdapterInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var \Elastica\ResultSet
     */
    private $resultSet;

    /**
     * @var SearchableInterface
     */
    private $searchable;

    public function __construct(SearchableInterface $searchable, Query $query)
    {
        $this->searchable = $searchable;
        $this->query = $query;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        if (!$this->resultSet) {
            return $this->searchable->search($this->query)->getTotalHits();
        }

        return $this->resultSet->getTotalHits();
    }

    /**
     * Returns the Elastica ResultSet. Will return null if getSlice has not yet been
     * called.
     *
     * @return \Elastica\ResultSet|null
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        return $this->resultSet = $this->searchable->search($this->query, array(
            'from' => $offset,
            'size' => $length
        ));
    }
}
