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

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Pagerfanta\Adapter\DoctrineORM\Paginator as LegacyPaginator;

/**
 * DoctrineORMAdapter.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @api
 */
class DoctrineORMAdapter implements AdapterInterface
{
    /**
     * @var \Doctrine\ORM\Tools\Pagination\Paginator|\Pagerfanta\Adapter\DoctrineORM\Paginator
     */
    private $paginator;

    /**
     * Constructor.
     *
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $query               A Doctrine ORM query or query builder.
     * @param Boolean            $fetchJoinCollection Whether the query joins a collection (true by default).
     *
     * @api
     */
    public function __construct($query, $fetchJoinCollection = true)
    {
        if (class_exists('Doctrine\ORM\Tools\Pagination\Paginator')) {
            $this->paginator = new DoctrinePaginator($query, $fetchJoinCollection);
        } else {
            $this->paginator = new LegacyPaginator($query, $fetchJoinCollection);
        }
    }

    /**
     * Returns the query
     *
     * @return Query
     *
     * @api
     */
    public function getQuery()
    {
        return $this->paginator->getQuery();
    }

    /**
     * Returns whether the query joins a collection.
     *
     * @return Boolean Whether the query joins a collection.
     */
    public function getFetchJoinCollection()
    {
        return $this->paginator->getFetchJoinCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return count($this->paginator);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $this->paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($length);

        return $this->paginator->getIterator();
    }
}
