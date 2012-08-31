<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\DoctrineCollectionAdapter;

class DoctrineCollectionTest extends \PHPUnit_Framework_TestCase
{
    protected $collection;
    protected $adapter;

    protected function setUp()
    {
        if (!interface_exists('Doctrine\Common\Collections\Collection')) {
            $this->markTestSkipped('Doctrine Common is not available');
        }

        $this->collection = $this
            ->getMockBuilder('Doctrine\Common\Collections\Collection')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->adapter = new DoctrineCollectionAdapter($this->collection);
    }

    public function testGetCollection()
    {
        $this->assertSame($this->collection, $this->adapter->getCollection());
    }

    public function testGetNbResults()
    {
        $this->collection
            ->expects($this->exactly(2))
            ->method('count')
            ->will($this->returnValue(120))
        ;

        $this->assertSame(120, $this->adapter->getNbResults());

        $this->adapter->setMaxResults(100);
        $this->assertSame(100, $this->adapter->getNbResults());
    }

    /**
     * @dataProvider getResultsProvider
     */
    public function testGetResults($offset, $length)
    {
        $this->collection
            ->expects($this->once())
            ->method('slice')
            ->with($offset, $length)
            ->will($this->returnValue($all = array(new \DateTime(), new \DateTime())))
        ;

        $this->assertSame($all, $this->adapter->getSlice($offset, $length));
    }

    public function getResultsProvider()
    {
        return array(
            array(2, 10),
            array(3, 2),
        );
    }
}
