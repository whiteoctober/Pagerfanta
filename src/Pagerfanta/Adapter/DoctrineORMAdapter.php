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
    
    /**
     * @var bool
     */
    private $fetchJoin = false;

    /**
     * Constructor.
     *
     * @param Query|QueryBuilder $query A Doctrine ORM query or query builder.
     * @param boolean $fetchJoinCollection Set to true if the passed query fetch joins a collection
     * 
     * @api
     */
    public function __construct($query, $fetchJoinCollection = false)
    {
        if ($query instanceof QueryBuilder) {
            $query = $query->getQuery();
        } else if (!($query instanceof Query)) {
            throw new \Pagerfanta\Exception\InvalidArgumentException("Expected either Doctrine\ORM\Query or Doctrine\ORM\QueryBuilder");
        }
        
        $this->query = $query;
        $this->fetchJoin = $fetchJoinCollection;
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
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        /* @var $countQuery Query */
        $countQuery = $this->cloneQuery($this->query);

        $countQuery->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\Doctrine\CountWalker'));
        $countQuery->setFirstResult(null)->setMaxResults(null);
        
        return $countQuery->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        if ($this->fetchJoin) {
            $subQuery = $this->cloneQuery($this->query);
            $subQuery->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\Doctrine\LimitSubqueryWalker'))
                ->setFirstResult($offset)
                ->setMaxResults($length);
            
            $ids = array_map('current', $subQuery->getScalarResult());
            
            // don't do this for an empty id array
            if (count($ids) > 0) {
                $namespace = 'pg_';
                $whereInQuery = $this->cloneQuery($this->query);

                $whereInQuery->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\Doctrine\WhereInWalker'));
                $whereInQuery->setHint('id.count', count($ids));
                $whereInQuery->setHint('pg.ns', $namespace);
                $whereInQuery->setFirstResult(null)->setMaxResults(null);
                foreach ($ids as $i => $id) {
                    $i = $i+1;
                    $whereInQuery->setParameter("{$namespace}_{$i}", $id);
                }
            } else {
                $whereInQuery = $this->query;
            }
            
            return $whereInQuery->getResult($this->query->getHydrationMode());
        } else {
            return $this->query->setMaxResults($length)
                               ->setFirstResult($offset)
                               ->getResult($this->query->getHydrationMode());
        }
    }
    
    /**
     * @param Query $query
     * @return Query
     */
    private function cloneQuery(Query $query)
    {
        /* @var $cloneQuery Query */
        $cloneQuery = clone $query;
        $cloneQuery->setParameters($query->getParameters());
        return $cloneQuery;
    } 
}
