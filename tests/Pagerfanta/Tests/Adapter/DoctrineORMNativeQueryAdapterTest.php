<?php

namespace Pagerfanta\Tests\Adapter;

use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\Tools\SchemaTool;
use Pagerfanta\Adapter\DoctrineORMNativeQueryAdapter;
use Pagerfanta\Tests\Adapter\DoctrineORM\DoctrineORMTestCase;
use Pagerfanta\Tests\Adapter\DoctrineORM\Group;
use Pagerfanta\Tests\Adapter\DoctrineORM\Person;
use Pagerfanta\Tests\Adapter\DoctrineORM\User;

class DoctrineORMNativeQueryAdapterTest extends DoctrineORMTestCase
{
    private $user1;
    private $user2;

    public function setUp()
    {
        parent::setUp();

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema([
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\User'),
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\Group'),
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\Person'),
        ]);

        $this->user1 = $user = new User();
        $this->user2 = $user2 = new User();
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
        $sql = "SELECT * FROM User u";

        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\User', 'u');

        $nq = $this->entityManager->createNativeQuery($sql, $rsm);

        $adapter = new DoctrineORMNativeQueryAdapter($nq);
        $this->assertEquals(2, $adapter->getNbResults());
    }

    public function testAdapterCountFetchJoin()
    {
        $count = function (NativeQuery $query) {
            $query->setSQL('SELECT COUNT(*) AS res FROM User u');
        };

        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\User', 'u');
        $rsm->addJoinedEntityFromClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\Group', 'g', 'u', 'groups', ['id' => 'user_id']);
        $sql = "SELECT " . $rsm->generateSelectClause(['u' => 'u', 'g' => 'g']) .
            " FROM User u INNER JOIN user_group ug ON u.id = ug.user_id LEFT JOIN groups g ON g.id = ug.group_id"
        ;

        $nq = $this->entityManager->createNativeQuery($sql, $rsm);
        $adapter = new DoctrineORMNativeQueryAdapter($nq, $count);
        $this->assertEquals(2, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $sql = "SELECT * FROM User u";

        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\User', 'u');

        $nq = $this->entityManager->createNativeQuery($sql, $rsm);

        $adapter = new DoctrineORMNativeQueryAdapter($nq);
        $this->assertEquals(1, count( $adapter->getSlice(0, 1)) );
        $this->assertEquals(2, count( $adapter->getSlice(0, 10)) );
        $this->assertEquals(1, count( $adapter->getSlice(1, 1)) );
    }

    public function testGetSliceFetchJoin()
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\User', 'u');
        $rsm->addJoinedEntityFromClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\Group', 'g', 'u', 'groups', ['id' => 'user_id']);
        $sql = "SELECT " . $rsm->generateSelectClause(['u' => 'u', 'g' => 'g']) .
            " FROM User u INNER JOIN user_group ug ON u.id = ug.user_id LEFT JOIN groups g ON g.id = ug.group_id"
        ;

        $nq = $this->entityManager->createNativeQuery($sql, $rsm);
        $adapter = new DoctrineORMNativeQueryAdapter($nq);
        $this->assertEquals(1, count( $adapter->getSlice(0, 1)) );
        $this->assertEquals(2, count( $adapter->getSlice(0, 10)) );
        $this->assertEquals(1, count( $adapter->getSlice(1, 1)) );
    }
}
