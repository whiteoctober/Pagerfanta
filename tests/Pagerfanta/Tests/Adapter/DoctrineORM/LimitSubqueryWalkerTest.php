<?php

namespace Pagerfanta\Tests\Adapter\DoctrineORM;

use Doctrine\ORM\Query;

class LimitSubqueryWalkerTest extends DoctrineORMTestCase
{
    public function testLimitSubquery()
    {
        $query = $this->entityManager->createQuery(
                        'SELECT p, c, a FROM Pagerfanta\Tests\Adapter\DoctrineORM\MyBlogPost p JOIN p.category c JOIN p.author a');
        $limitQuery = clone $query;
        $limitQuery->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\DoctrineORM\LimitSubqueryWalker'));

        $this->assertEquals(
                "SELECT DISTINCT m0_.id AS id0 FROM MyBlogPost m0_ INNER JOIN Category c1_ ON m0_.category_id = c1_.id INNER JOIN Author a2_ ON m0_.author_id = a2_.id", $limitQuery->getSql()
        );
    }

    public function testCountQuery_MixedResultsWithName()
    {
        $query = $this->entityManager->createQuery(
                        'SELECT a, sum(a.name) as foo FROM Pagerfanta\Tests\Adapter\DoctrineORM\Author a');
        $limitQuery = clone $query;
        $limitQuery->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\DoctrineORM\LimitSubqueryWalker'));

        $this->assertEquals(
                "SELECT DISTINCT a0_.id AS id0, sum(a0_.name) AS sclr1 FROM Author a0_", $limitQuery->getSql()
        );
    }
}
