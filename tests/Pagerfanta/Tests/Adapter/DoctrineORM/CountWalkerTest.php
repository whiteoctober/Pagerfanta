<?php

namespace Pagerfanta\Tests\Adapter\DoctrineORM;

use Doctrine\ORM\Query;

class CountWalkerTest extends DoctrineORMTestCase
{
    public function testCountQuery()
    {
        $query = $this->entityManager->createQuery(
                        'SELECT p, c, a FROM Pagerfanta\Tests\Adapter\DoctrineORM\BlogPost p JOIN p.category c JOIN p.author a');
        $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\DoctrineORM\CountWalker'));
        $query->setFirstResult(null)->setMaxResults(null);

        $this->assertEquals(
                "SELECT count(DISTINCT b0_.id) AS sclr0 FROM BlogPost b0_ INNER JOIN Category c1_ ON b0_.category_id = c1_.id INNER JOIN Author a2_ ON b0_.author_id = a2_.id", $query->getSql()
        );
    }

    public function testCountQuery_MixedResultsWithName()
    {
        $query = $this->entityManager->createQuery(
                        'SELECT a, sum(a.name) as foo FROM Pagerfanta\Tests\Adapter\DoctrineORM\Author a');
        $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\DoctrineORM\CountWalker'));
        $query->setFirstResult(null)->setMaxResults(null);

        $this->assertEquals(
                "SELECT count(DISTINCT a0_.id) AS sclr0 FROM Author a0_", $query->getSql()
        );
    }

    public function testCountQuery_KeepsGroupBy()
    {
        $query = $this->entityManager->createQuery(
                        'SELECT b FROM Pagerfanta\Tests\Adapter\DoctrineORM\BlogPost b GROUP BY b.id');
        $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\DoctrineORM\CountWalker'));
        $query->setFirstResult(null)->setMaxResults(null);

        $this->assertEquals(
                "SELECT count(DISTINCT b0_.id) AS sclr0 FROM BlogPost b0_ GROUP BY b0_.id", $query->getSql()
        );
    }

    public function testCountQuery_RemovesOrderBy()
    {
        $query = $this->entityManager->createQuery(
                        'SELECT p, c, a FROM Pagerfanta\Tests\Adapter\DoctrineORM\BlogPost p JOIN p.category c JOIN p.author a ORDER BY a.name');
        $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\DoctrineORM\CountWalker'));
        $query->setFirstResult(null)->setMaxResults(null);

        $this->assertEquals(
                "SELECT count(DISTINCT b0_.id) AS sclr0 FROM BlogPost b0_ INNER JOIN Category c1_ ON b0_.category_id = c1_.id INNER JOIN Author a2_ ON b0_.author_id = a2_.id", $query->getSql()
        );
    }

    public function testCountQuery_RemovesLimits()
    {
        $query = $this->entityManager->createQuery(
                        'SELECT p, c, a FROM Pagerfanta\Tests\Adapter\DoctrineORM\BlogPost p JOIN p.category c JOIN p.author a');
        $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, array('Pagerfanta\Adapter\DoctrineORM\CountWalker'));
        $query->setFirstResult(null)->setMaxResults(null);

        $this->assertEquals(
                "SELECT count(DISTINCT b0_.id) AS sclr0 FROM BlogPost b0_ INNER JOIN Category c1_ ON b0_.category_id = c1_.id INNER JOIN Author a2_ ON b0_.author_id = a2_.id", $query->getSql()
        );
    }
}
