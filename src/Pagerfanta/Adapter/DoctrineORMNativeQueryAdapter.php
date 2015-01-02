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

use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMapping;
use Pagerfanta\Exception\InvalidArgumentException;

/**
 * @author Maxime Veber <nek.dev@gmail.com>
 */
class DoctrineORMNativeQueryAdapter implements AdapterInterface
{
    /**
     * @var NativeQuery
     */
    private $nativeQuery;

    /**
     * @var callable
     */
    private $countQueryBuilderModifier;

    /**
     * @var callable
     */
    private $sliceQueryBuilderModifier;

    /**
     * @param NativeQuery  $query                     A DBAL query builder.
     * @param callable     $countQueryBuilderModifier A callable to modify the query to count.
     * @param callable     $sliceQueryBuilderModifier A callable to modify the query to slice it (the default should do the job most part of the time).
     * @throws InvalidArgumentException
     */
    public function __construct(NativeQuery $query, $countQueryBuilderModifier = null, $sliceQueryBuilderModifier = null)
    {
        if (strpos(strtolower($query->getSQL()), 'select') !== 0) {
            throw new InvalidArgumentException('Only SELECT queries can be paginated.');
        }

        if ($countQueryBuilderModifier !== null && !is_callable($countQueryBuilderModifier)) {
            throw new InvalidArgumentException('The count query builder modifier must be a callable.');
        }
        if ($sliceQueryBuilderModifier !== null && !is_callable($sliceQueryBuilderModifier)) {
            throw new InvalidArgumentException('The slice query builder modifier must be a callable.');
        }

        $this->nativeQuery = $query;
        $this->countQueryBuilderModifier = $countQueryBuilderModifier;
        $this->sliceQueryBuilderModifier = $sliceQueryBuilderModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $query = $this->cloneQuery();
        $this->countQueryBuilder($query);

        if ($this->countQueryBuilderModifier !== null) {
            call_user_func($this->countQueryBuilderModifier, $query);
        }

        $res = $query->getScalarResult();

        return $res[0]['res'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $query = $this->cloneQuery();

        if ($this->sliceQueryBuilderModifier !== null) {
            call_user_func($this->sliceQueryBuilderModifier, $query, $offset, $length);
        } else {
            $this->sliceQueryBuilder($query, $offset, $length);
        }

        return $query->getResult();
    }

    private function cloneQuery()
    {
        $query = clone $this->nativeQuery;
        $query->setParameters($this->nativeQuery->getParameters());

        return $query;
    }

    private function countQueryBuilder(NativeQuery $query)
    {
        $sql = explode(' FROM ', $query->getSql());
        $sql[0] = 'SELECT COUNT(*) AS res';
        $sql = implode(' FROM ', $sql);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('res', 'res');

        $query->setResultSetMapping($rsm);
        $query->setSQL($sql);
    }

    private function sliceQueryBuilder(NativeQuery $query, $offset, $length)
    {
        $sql = $query->getSql();
        $sql .= ' LIMIT ' . $length . ' OFFSET '. $offset;
        $query->setSQL($sql);
    }
}
