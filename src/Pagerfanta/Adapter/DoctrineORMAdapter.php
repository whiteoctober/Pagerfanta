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

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\NoResultException;

/**
 * DoctrineORMAdapter.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 *
 * @api
 */
class DoctrineORMAdapter implements AdapterInterface
{
    /**
     * @var Query
     */
    private $query;

    private $fetchJoinCollection;

    /**
     * Constructor.
     *
     * @param Query   $query               A Doctrine ORM query or query builder.
     * @param Boolean $fetchJoinCollection Whether the query joins a collection (false by default).
     *
     * @api
     */
    public function __construct($query, $fetchJoinCollection = false)
    {
        $this->query = $query;
        $this->fetchJoin = (Boolean) $fetchJoinCollection;
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
        return $this->query;
    }

    /**
     * Returns whether the query joins a collection.
     *
     * @return Boolean Whether the query joins a collection.
     */
    public function getFetchJoinCollection()
    {
        $this->fetchJoinCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        /* @var $countQuery Query */
        $countQuery = $this->cloneQuery($this->query);

        $countQuery->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\DoctrineORM\CountWalker'));
        $countQuery->setFirstResult(null)->setMaxResults(null);

        try {
            $data =  $countQuery->getScalarResult();
            $data = array_map('current', $data);
            return array_sum($data);
        } catch(NoResultException $e) {
            return 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        if ($this->fetchJoinCollection) {
            $subQuery = $this->cloneQuery($this->query);
            $subQuery->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\DoctrineORM\LimitSubqueryWalker'))
                ->setFirstResult($offset)
                ->setMaxResults($length);

            $ids = array_map('current', $subQuery->getScalarResult());

            $whereInQuery = $this->cloneQuery($this->query);
            // don't do this for an empty id array
            if (count($ids) > 0) {
                $namespace = 'pg_';

                $whereInQuery->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\DoctrineORM\WhereInWalker'));
                $whereInQuery->setHint('id.count', count($ids));
                $whereInQuery->setHint('pg.ns', $namespace);
                $whereInQuery->setFirstResult(null)->setMaxResults(null);
                foreach ($ids as $i => $id) {
                    $i++;
                    $whereInQuery->setParameter("{$namespace}_{$i}", $id);
                }
            }

            return $whereInQuery->getResult($this->query->getHydrationMode());
        }

        return $this->cloneQuery($this->query)
            ->setMaxResults($length)
            ->setFirstResult($offset)
            ->getResult($this->query->getHydrationMode())
        ;
    }

    /**
     * Clones a query.
     *
     * @param Query $query The query.
     *
     * @return Query The cloned query.
     */
    private function cloneQuery(Query $query)
    {
        /* @var $cloneQuery Query */
        $cloneQuery = clone $query;
        $cloneQuery->setParameters($query->getParameters());

        return $cloneQuery;
    }
}
