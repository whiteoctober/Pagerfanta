<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\DoctrineSelectableAdapter;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Collections\Collection;

class DoctrineSelectableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var Selectable
     */
    protected $selectable;

    /**
     * @var DoctrineSelectableAdapter
     */
    protected $adapter;

    protected function setUp()
    {
        if (version_compare(\Doctrine\ORM\Version::VERSION, '2.3', '<')) {
            $this->markTestSkipped('This test can only be run using Doctrine >= 2.3');
        }

        $this->selectable = $this
            ->getMockBuilder('Doctrine\Common\Collections\Selectable')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->collection = $this
                ->getMockBuilder('Doctrine\Common\Collections\Collection')
                ->disableOriginalConstructor()
                ->getMock()
        ;

        $this->collection
            ->expects($this->any())
            ->method('count')
            ->will($this->returnValue(10))
        ;

        $this->adapter = new DoctrineSelectableAdapter($this->selectable);
    }

    public function testGetNbResults()
    {
        $this->selectable
            ->expects($this->once())
            ->method('matching')
            ->will($this->returnValue($this->collection))
        ;

        $this->assertSame(10, $this->adapter->getNbResults());
    }

    /**
     * @dataProvider getResultsProvider
     */
    public function testGetResults($offset, $limit)
    {

        $this->selectable
            ->expects($this->once())
            ->method('matching')
            ->will(
                $this->returnValue($data = array(
                        new \DateTime(),
                        new \DateTime(),
                    )
                )
            )
        ;

        $this->assertSame($data, $this->adapter->getSlice($offset, $limit));
    }

    public function getResultsProvider()
    {
        return array(
            array(2, 10),
            array(3, 2),
        );
    }
}
