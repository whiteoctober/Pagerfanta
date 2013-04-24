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
use Pagerfanta\Exception\InvalidArgumentException;

/**
 * @author Michael Williams <michael@whizdevelopment.com>
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DoctrineDbalAdapter implements AdapterInterface
{
    private $queryBuilder;
    private $countQueryBuilderModifier;

    /**
     * Constructor.
     *
     * @param QueryBuilder $queryBuilder              A DBAL query builder.
     * @param callable     $countQueryBuilderModifier A callable to modifier the query builder to count.
     */
    public function __construct(QueryBuilder $queryBuilder, $countQueryBuilderModifier)
    {
        if ($queryBuilder->getType() !== QueryBuilder::SELECT) {
            throw new InvalidArgumentException('Only SELECT queries can be paginated.');
        }

        if (!is_callable($countQueryBuilderModifier)) {
            throw new InvalidArgumentException('The count query builder modifier must be a callable.');
        }

        $this->queryBuilder = clone $queryBuilder;
        $this->countQueryBuilderModifier = $countQueryBuilderModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $qb = $this->prepageCountQueryBuilder();
        $result = $qb->execute()->fetchColumn();

        return (int) $result;
    }

    private function prepageCountQueryBuilder()
    {
        $qb = clone $this->queryBuilder;
        call_user_func($this->countQueryBuilderModifier, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $qb = clone $this->queryBuilder;
        $result = $qb->setMaxResults($length)
            ->setFirstResult($offset)
            ->execute();

        return $result->fetchAll();
    }
}