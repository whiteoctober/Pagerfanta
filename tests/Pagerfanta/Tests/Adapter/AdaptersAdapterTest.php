<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Pagerfanta\Tests\Adapter;

use Pagerfanta\Adapter\AdaptersAdapter;
use Pagerfanta\Adapter\ArrayAdapter;

class AdaptersAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdaptersAdapter
     */
    protected $adapter;
    protected $array1;
    protected $array2;
    protected $array3;

    public function setUp()
    {
        $this->array1 = array(
            3,
            5,
            1,
        );
        $this->array2 = array(
            56,
        );
        $this->array3 = array(
            78,
            90,
            12,
            8,
            4,
        );
        $this->adapter = new AdaptersAdapter(array(
            new ArrayAdapter($this->array1),
            new ArrayAdapter($this->array2),
            new ArrayAdapter($this->array3),
        ));
    }

    public function testGetNbResult()
    {
        $this->assertEquals(
            count($this->array1) + count($this->array2) + count($this->array3),
            $this->adapter->getNbResults()
        );
    }

    public function testGetSlice()
    {
        $this->assertEquals(
            array_merge($this->array1, $this->array2, $this->array3),
            $this->adapter->getSlice(0, 9)
        );
        $this->assertEquals(
            array_merge($this->array1, $this->array2, $this->array3),
            $this->adapter->getSlice(0, 43)
        );
        $this->assertEquals(
            array_merge($this->array1, array_slice($this->array2, 0, 1)),
            $this->adapter->getSlice(0, 4)
        );
        $this->assertEquals(
            array_merge($this->array1, $this->array2, array_slice($this->array3, 0, 3)),
            $this->adapter->getSlice(0, 7)
        );
        $this->assertEquals(
            array_merge(array_slice($this->array1, 2), $this->array2, $this->array3),
            $this->adapter->getSlice(2, 9)
        );
        $this->assertEquals(
            array_merge(array_slice($this->array1, 2), $this->array2, array_slice($this->array3, 0, 3)),
            $this->adapter->getSlice(2, 5)
        );
        $this->assertEquals(
            array_merge($this->array2, array_slice($this->array3, 0, 2)),
            $this->adapter->getSlice(3, 3)
        );
    }
}
