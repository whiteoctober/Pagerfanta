<?php

namespace Pagerfanta\Tests\Adapter;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

use Doctrine\ORM\Tools\SchemaTool;
use Pagerfanta\Adapter\DoctrineORMNativeQueryAdapter;
use Pagerfanta\Tests\Adapter\DoctrineORM\DoctrineORMTestCase;
use Pagerfanta\Tests\Adapter\DoctrineORM\User;
use Pagerfanta\Tests\Adapter\DoctrineORM\Group;
use Pagerfanta\Tests\Adapter\DoctrineORM\Person;

class DoctrineORMNativeQueryAdapterTest extends DoctrineORMTestCase
{
    private $user1;
    private $user2;

    public function setUp()
    {
        parent::setUp();

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema(array(
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\User'),
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\Group'),
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\Person'),
        ));

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

    protected function getUserEntityAdapter()
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\User', 'u');

        $query = new QueryBuilder($this->entityManager->getConnection());
        $query->select($rsm->generateSelectClause(['u' => 'U']));
        $query->from('user', 'U');

        return new DoctrineORMNativeQueryAdapter($query, $this->entityManager, $rsm, function($query){
            $query->select('COUNT(U.id) AS user_count')->setMaxResults(1);
        });
    }

    protected function getUserGroupJoinedEntityAdapter()
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\User', 'u');
        $rsm->addJoinedEntityFromClassMetadata('Pagerfanta\Tests\Adapter\DoctrineORM\Group', 'g', 'u', 'groups', [
            'id' => 'group_id_x']);

        $query = new QueryBuilder($this->entityManager->getConnection());
        $query->select($rsm->generateSelectClause(['u' => 'U', 'g' => 'G']));
        $query->from('user', 'U');
        $query->innerJoin('U', 'user_group', 'UG', 'U.id = UG.user_id');
        $query->innerJoin('UG', 'groups', 'G', 'G.id = UG.group_id');

        return new DoctrineORMNativeQueryAdapter($query, $this->entityManager, $rsm, function($query){
            $query->select('COUNT(DISTINCT U.id) AS user_count')->setMaxResults(1);
        });
    }

    public function testAdapterCount()
    {
        $this->assertEquals(2, $this->getUserEntityAdapter()->getNbResults());
    }

    public function testAdapterCountFetchJoin()
    {
        $this->assertEquals(2, $this->getUserGroupJoinedEntityAdapter()->getNbResults());
    }

    public function testGetSlice()
    {
        $adapter = $this->getUserEntityAdapter();
        $this->assertEquals(1, count( $adapter->getSlice(0, 1)) );
        $this->assertEquals(2, count( $adapter->getSlice(0, 10)) );
        $this->assertEquals(1, count( $adapter->getSlice(1, 1)) );
    }

    public function testGetSliceFetchJoin()
    {
        $adapter = $this->getUserGroupJoinedEntityAdapter();
        $this->assertEquals(1, count( $adapter->getSlice(0, 1)) );
        $this->assertEquals(2, count( $adapter->getSlice(0, 10)) );
        $this->assertEquals(1, count( $adapter->getSlice(1, 1)) );
    }

}