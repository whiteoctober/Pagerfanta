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

    /**
     * @var array
     */
    private $options;

    /**
     * @var int|null
     *
     * Used to limit the number of totalHits returned by ES.
     * For more information, see: https://github.com/whiteoctober/Pagerfanta/pull/213#issue-87631892
     */
    private $maxResults;

    public function __construct(SearchableInterface $searchable, Query $query, array $options = array(), $maxResults = null)
    {
        $this->searchable = $searchable;
        $this->query = $query;
        $this->options = $options;
        $this->maxResults = $maxResults;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        if (!$this->resultSet) {
            $totalHits = $this->searchable->count($this->query);
        } else {
            $totalHits = $this->resultSet->getTotalHits();
        }

        if (null === $this->maxResults) {
            return $totalHits;
        }

        return min($totalHits, $this->maxResults);
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
        return $this->resultSet = $this->searchable->search($this->query, array_merge($this->options, array(
            'from' => $offset,
            'size' => $length
        )));
    }
}
