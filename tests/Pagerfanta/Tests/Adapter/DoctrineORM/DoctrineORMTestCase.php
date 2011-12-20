<?php

namespace Pagerfanta\Tests\Adapter\DoctrineORM;

abstract class DoctrineORMTestCase extends \PHPUnit_Framework_TestCase
{
    public $entityManager;

    public function setUp()
    {
        if (!class_exists('Doctrine\ORM\EntityManager')) {
            $this->markTestSkipped('Doctrine ORM is not available');
        }

        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        $config->setProxyDir(__DIR__ . '/_files');
        $config->setProxyNamespace('Pagerfanta\Tests\Adapter\DoctrineORM\Proxies');
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver());

        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $this->entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);
    }
}


/**
 * @Entity
 */
class MyBlogPost
{

    /** @Id @column(type="integer") @generatedValue */
    public $id;
    /**
     * @ManyToOne(targetEntity="Author")
     */
    public $author;
    /**
     * @ManyToOne(targetEntity="Category")
     */
    public $category;
}

/**
 * @Entity
 */
class MyAuthor
{

    /** @Id @column(type="integer") @generatedValue */
    public $id;

}

/**
 * @Entity
 */
class MyCategory
{

    /** @id @column(type="integer") @generatedValue */
    public $id;

}


/**
 * @Entity
 */
class BlogPost
{

    /** @Id @column(type="integer") @generatedValue */
    public $id;
    /**
     * @ManyToOne(targetEntity="Author")
     */
    public $author;
    /**
     * @ManyToOne(targetEntity="Category")
     */
    public $category;
}

/**
 * @Entity
 */
class Author
{

    /** @Id @column(type="integer") @generatedValue */
    public $id;
    /** @Column(type="string") */
    public $name;

}

/**
 * @Entity
 */
class Person
{

    /** @Id @column(type="integer") @generatedValue */
    public $id;
    /** @Column(type="string") */
    public $name;
    /** @Column(type="string") */
    public $biography;

}

/**
 * @Entity
 */
class Category
{

    /** @id @column(type="integer") @generatedValue */
    public $id;

}


/** @Entity @Table(name="groups") */
class Group
{

    /** @Id @column(type="integer") @generatedValue */
    public $id;
    /** @ManyToMany(targetEntity="User", mappedBy="groups") */
    public $users;
}

/** @Entity */
class User
{

    /** @Id @column(type="integer") @generatedValue */
    public $id;
    /**
     * @ManyToMany(targetEntity="Group", inversedBy="users")
     * @JoinTable(
     *  name="user_group",
     *  joinColumns = {@JoinColumn(name="user_id", referencedColumnName="id")},
     *  inverseJoinColumns = {@JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    public $groups;
}
