<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Tugrul Topuz <tugrultopuz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Pagerfanta\Adapter;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Exception\InvalidArgumentException;

/**
 * DoctrineORMNativeQueryAdapter.
 *
 * @author Tugrul Topuz <tugrultopuz@gmail.com>
 */
class DoctrineORMNativeQueryAdapter implements AdapterInterface
{

    /**
     * @var ResultSetMapping
     */
    protected $resultSetMapping;


    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;


    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var callable
     */
    protected $countQueryBuilderModifier;

    /**
     * Constructor.
     *
     * @param QueryBuilder $queryBuilder              A DBAL query builder.
     * @param EntityManagerInterface $entityManager   To create NativeQuery instance
     * @param ResultSetMapping $resultSetMapping      ORM Mapping
     * @param callable     $countQueryBuilderModifier A callable to modifier the query builder to count.
     */
    public function __construct(QueryBuilder $queryBuilder, EntityManagerInterface $entityManager,
                                ResultSetMapping $resultSetMapping, $countQueryBuilderModifier)
    {
        if ($queryBuilder->getType() !== QueryBuilder::SELECT) {
            throw new InvalidArgumentException('Only SELECT queries can be paginated.');
        }

        if (!is_callable($countQueryBuilderModifier)) {
            throw new InvalidArgumentException('The count query builder modifier must be a callable.');
        }

        $this->queryBuilder = clone $queryBuilder;
        $this->entityManager = $entityManager;
        $this->resultSetMapping = $resultSetMapping;
        $this->countQueryBuilderModifier = $countQueryBuilderModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $qb = $this->prepareCountQueryBuilder();
        $result = $qb->execute()->fetchColumn();

        return (int) $result;
    }

    protected function prepareCountQueryBuilder()
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

        $qb->setMaxResults($length)
            ->setFirstResult($offset);

        $nativeQuery = $this->entityManager->createNativeQuery($qb->getSQL(), $this->resultSetMapping);
        $nativeQuery->setParameters($qb->getParameters());
        return $nativeQuery->getResult();
    }


}

