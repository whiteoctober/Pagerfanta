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
    private $query;
    private $countQueryModifier;

    /**
     * Constructor.
     *
     * @param QueryBuilder $query              A DBAL query builder.
     * @param callable     $countQueryModifier A callable to modifier the query to count.
     */
    public function __construct(QueryBuilder $query, $countQueryModifier)
    {
        if ($query->getType() !== QueryBuilder::SELECT) {
            throw new InvalidArgumentException('Only SELECT queries can be paginated.');
        }

        if (!is_callable($countQueryModifier)) {
            throw new InvalidArgumentException('The count query modifier must be a callable.');
        }

        $this->query = clone $query;
        $this->countQueryModifier = $countQueryModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $q = $this->prepageCountQuery();
        $result = $q->execute()->fetchColumn();

        return (int) $result;
    }

    private function prepageCountQuery()
    {
        $q = clone $this->query;
        call_user_func($this->countQueryModifier, $q);

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $q = clone $this->query;
        $result = $q->setMaxResults($length)
            ->setFirstResult($offset)
            ->execute();

        return $result->fetchAll();
    }
}