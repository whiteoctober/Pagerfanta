<?php

namespace Pagerfanta\Tests\Adapter;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\SchemaTool;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Tests\Adapter\Doctrine\User;
use Pagerfanta\Tests\Adapter\Doctrine\Group;

class DoctrineORMAdapterTest extends Doctrine\DoctrineTestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema(array(
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\Doctrine\User'),
            $this->entityManager->getClassMetadata('Pagerfanta\Tests\Adapter\Doctrine\Group'),
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
        
        $this->entityManager->persist($user);
        $this->entityManager->persist($user2);
        $this->entityManager->persist($group1);
        $this->entityManager->persist($group2);
        $this->entityManager->persist($group3);
        $this->entityManager->flush();
    }
    
    public function testAdapterCount()
    {
        $dql = "SELECT u FROM Pagerfanta\Tests\Adapter\Doctrine\User u";
        $query = $this->entityManager->createQuery($dql);
        
        $adapter = new DoctrineORMAdapter($query);
        $this->assertEquals(2, $adapter->getNbResults());
    }
    
    public function testAdapterCountFetchJoin()
    {
        $dql = "SELECT u, g FROM Pagerfanta\Tests\Adapter\Doctrine\User u INNER JOIN u.groups g";
        $query = $this->entityManager->createQuery($dql);
        
        $adapter = new DoctrineORMAdapter($query);
        $this->assertEquals(2, $adapter->getNbResults());
    }
    
    public function testGetSlice()
    {
        $dql = "SELECT u FROM Pagerfanta\Tests\Adapter\Doctrine\User u";
        $query = $this->entityManager->createQuery($dql);
        
        $adapter = new DoctrineORMAdapter($query);
        $this->assertEquals(1, count( $adapter->getSlice(0, 1)) );
        $this->assertEquals(2, count( $adapter->getSlice(0, 10)) );
        $this->assertEquals(1, count( $adapter->getSlice(1, 1)) );
    }
    
    public function testGetSliceFetchJoin()
    {
        $dql = "SELECT u FROM Pagerfanta\Tests\Adapter\Doctrine\User u INNER JOIN u.groups g";
        $query = $this->entityManager->createQuery($dql);
        
        $adapter = new DoctrineORMAdapter($query, true);
        $this->assertEquals(1, count( $adapter->getSlice(0, 1)) );
        $this->assertEquals(2, count( $adapter->getSlice(0, 10)) );
        $this->assertEquals(1, count( $adapter->getSlice(1, 1)) );
    }
}