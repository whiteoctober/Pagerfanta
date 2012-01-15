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

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * DoctrineDBALAdapter.
 *
 * @author Michael Williams <michael@whizdevelopment.com>
 *
 * @api
 */
class DoctrineDBALAdapter implements AdapterInterface
{
    private $queryBuilder;

    private $primaryKey;

    /**
     * Constructor.
     *
     * @param Builder $queryBuilder A DBAL query builder.
     * @param The primary key for the primary DB table in the query, example, "id"
     *
     * @api
     */
    public function __construct(QueryBuilder $queryBuilder, $primaryKey)
    {
        $this->queryBuilder = $queryBuilder;
        $this->primaryKey = $primaryKey;
    }

    /**
     * Returns the query builder.
     *
     * @return Builder The query builder.
     *
     * @api
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $query = clone $this->queryBuilder;
        $statement = $query->select('COUNT(DISTINCT ' . $this->primaryKey . ') AS total_results')
            ->setMaxResults(1)
            ->execute()
        ;

        return ($results = $statement->fetchColumn(0)) ? $results : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $query = clone $this->queryBuilder;

        $result =  $query->setMaxResults($length)
            ->setFirstResult($offset)
            ->execute()
        ;

        return $result->fetchAll();
    }
}