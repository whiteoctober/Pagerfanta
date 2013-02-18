<?php

namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\ArrayAdapter;

class ArrayAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $array;
    protected $adapter;

    protected function setUp()
    {
        $this->array = array();
        for ($i = 0; $i < 10; $i++) {
            $this->array[] = mt_rand(11111, 99999);
        }
        $this->adapter = new ArrayAdapter($this->array);
    }

    public function testGetArray()
    {
        $this->assertSame($this->array, $this->adapter->getArray());
    }

    public function testGetNbResults()
    {
        $this->assertSame(count($this->array), $this->adapter->getNbResults());

        $this->adapter->setMaxResults(3);
        $this->assertSame(3, $this->adapter->getNbResults());
    }

    /**
     * @dataProvider getResultsProvider
     */
    public function testGetResults($offset, $length)
    {
        $this->assertSame(array_slice($this->array, $offset, $length), $this->adapter->getSlice($offset, $length));
    }

    public function getResultsProvider()
    {
        return array(
            array(2, 10),
            array(3, 2),
        );
    }
}
