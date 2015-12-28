<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Omni\CommonBundle\Tests\Pagerfanta;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\MappingAdapter;

class MappingAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  MappingAdapter */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new MappingAdapter(new ArrayAdapter(array(
            34,
            45,
            67,
            12
        )), function ($item) {
            return $item + 1;
        });
    }

    public function testGetNbResults()
    {
        $this->assertEquals(4, $this->adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $this->assertEquals(array(
            35,
            46,
            68,
            13
        ), $this->adapter->getSlice(0, 34));
        $this->assertEquals(array(
            35,
            46,
        ), $this->adapter->getSlice(0, 2));
        $this->assertEquals(array(
            68,
            13,
        ), $this->adapter->getSlice(2, 5));
        $this->assertEquals(array(
            68,
        ), $this->adapter->getSlice(2, 1));
    }
}
