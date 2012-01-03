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
        $limitQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Pagerfanta\Adapter\DoctrineORM\LimitSubqueryWalker');

        $this->assertEquals(
                "SELECT DISTINCT id0 FROM (SELECT m0_.id AS id0, c1_.id AS id1, a2_.id AS id2, a2_.name AS name3, m0_.author_id AS author_id4, m0_.category_id AS category_id5 FROM MyBlogPost m0_ INNER JOIN Category c1_ ON m0_.category_id = c1_.id INNER JOIN Author a2_ ON m0_.author_id = a2_.id) AS _dctrn_result", $limitQuery->getSql()
        );
    }

    public function testCountQuery_MixedResultsWithName()
    {
        $query = $this->entityManager->createQuery(
                        'SELECT a, sum(a.name) as foo FROM Pagerfanta\Tests\Adapter\DoctrineORM\Author a');
        $limitQuery = clone $query;
        $limitQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Pagerfanta\Adapter\DoctrineORM\LimitSubqueryWalker');

        $this->assertEquals(
                "SELECT DISTINCT id0 FROM (SELECT a0_.id AS id0, a0_.name AS name1, sum(a0_.name) AS sclr2 FROM Author a0_) AS _dctrn_result", $limitQuery->getSql()
        );
    }
}
