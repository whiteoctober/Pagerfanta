<?php

namespace Pagerfanta\Tests\Adapter;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\SchemaTool;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Tests\Adapter\DoctrineORM\DoctrineORMTestCase;
use Pagerfanta\Tests\Adapter\DoctrineORM\User;
use Pagerfanta\Tests\Adapter\DoctrineORM\Group;
use Pagerfanta\Tests\Adapter\DoctrineORM\Person;

class DoctrineORMAdapterTest extends DoctrineORMTestCase
{
    public function setUp()
    {
        parent::setUp();

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema(array(
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\User'),
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\Group'),
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\Person'),
        ));

        $user = new User();
        $user2 = new User();
        $group1 = new Group();
        $group2 = new Group();
        $group3 = new Group();
        $user->groups[] = $group1;
        $user->groups[] = $group2;
        $user->groups[] = $group3;
        $user2->groups[] = $group1;
        $author1 = new Person();
        $author1->name = 'Foo';
        $author1->biography = 'Baz bar';
        $author2 = new Person();
        $author2->name = 'Bar';
        $author2->biography = 'Bar baz';

        $this->entityManager->persist($user);
        $this->entityManager->persist($user2);
        $this->entityManager->persist($group1);
        $this->entityManager->persist($group2);
        $this->entityManager->persist($group3);
        $this->entityManager->persist($author1);
        $this->entityManager->persist($author2);
        $this->entityManager->flush();
    }

    public function testAdapterCount()
    {
        $dql = "SELECT u FROM Pagerfanta\Tests\Adapter\DoctrineORM\User u";
        $query = $this->entityManager->createQuery($dql);

        $adapter = new DoctrineORMAdapter($query);
        $this->assertEquals(2, $adapter->getNbResults());
    }

    public function testAdapterCountFetchJoin()
    {
        $dql = "SELECT u, g FROM Pagerfanta\Tests\Adapter\DoctrineORM\User u INNER JOIN u.groups g";
        $query = $this->entityManager->createQuery($dql);

        $adapter = new DoctrineORMAdapter($query);
        $this->assertEquals(2, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $dql = "SELECT u FROM Pagerfanta\Tests\Adapter\DoctrineORM\User u";
        $query = $this->entityManager->createQuery($dql);

        $adapter = new DoctrineORMAdapter($query);
        $this->assertEquals(1, count( $adapter->getSlice(0, 1)) );
        $this->assertEquals(2, count( $adapter->getSlice(0, 10)) );
        $this->assertEquals(1, count( $adapter->getSlice(1, 1)) );
    }

    public function testGetSliceFetchJoin()
    {
        $dql = "SELECT u FROM Pagerfanta\Tests\Adapter\DoctrineORM\User u INNER JOIN u.groups g";
        $query = $this->entityManager->createQuery($dql);

        $adapter = new DoctrineORMAdapter($query, true);
        $this->assertEquals(1, count( $adapter->getSlice(0, 1)) );
        $this->assertEquals(2, count( $adapter->getSlice(0, 10)) );
        $this->assertEquals(1, count( $adapter->getSlice(1, 1)) );
    }

    public function testCountAfterSlice()
    {
        $dql = "SELECT u FROM Pagerfanta\Tests\Adapter\DoctrineORM\User u";
        $query = $this->entityManager->createQuery($dql);

        $adapter = new DoctrineORMAdapter($query);
        $adapter->getSlice(0, 1);
        $this->assertEquals(2, $adapter->getNbResults() );
    }

    public function testMultipleRoot()
    {
        $this->markTestIncomplete('Multiple roots are not supported currently');
        $dql = <<<DQL
        SELECT u, g FROM
            Pagerfanta\Tests\Adapter\DoctrineORM\User u,
            Pagerfanta\Tests\Adapter\DoctrineORM\Group g
DQL;
        $query = $this->entityManager->createQuery($dql);

        $adapter = new DoctrineORMAdapter($query);
        $this->assertCount(5, $adapter->getSlice(0, 100));
        $this->assertCount(4, $adapter->getSlice(0, 4));
        $this->assertEquals(5, $adapter->getNbResults());
    }

    public function testMixedResult()
    {
        $dql = <<<DQL
        SELECT p, p.name FROM
            Pagerfanta\Tests\Adapter\DoctrineORM\Person p
DQL;
        $query = $this->entityManager->createQuery($dql);

        $adapter = new DoctrineORMAdapter($query);
        $this->assertEquals(2, $adapter->getNbResults());
        $items = $adapter->getSlice(0, 10);
        $this->assertCount(2, $items);
        $this->assertArrayHasKey('name', $items[0]);
    }

    public function testCaseBasedQuery()
    {
        if (version_compare(\Doctrine\ORM\Version::VERSION, '2.2.0-DEV', '<')) {
            $this->markTestSkipped('Only recent orm version can test against this query.');
        }

        $dql = <<<DQL
            SELECT p,
              CASE
                WHEN p.name LIKE :keyword
                  AND p.biography LIKE :keyword
                THEN 0

                WHEN p.name LIKE :keyword
                THEN 1

                WHEN p.biography LIKE :keyword
                THEN 2

                ELSE 3
              END AS relevance
            FROM Pagerfanta\Tests\Adapter\DoctrineORM\Person p
            WHERE (
              p.name LIKE :keyword
              OR p.biography LIKE :keyword
            )
            GROUP BY p.id
            ORDER BY relevance ASC, p.id DESC
DQL;
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('keyword', '%Foo%');

        $adapter = new DoctrineORMAdapter($query);
        $this->assertEquals(1, $adapter->getNbResults());
        $items = $adapter->getSlice(0, 10);
        $this->assertEquals('Foo', $items[0][0]->name);
        $this->assertEquals(1, $items[0]['relevance']);
    }
}
