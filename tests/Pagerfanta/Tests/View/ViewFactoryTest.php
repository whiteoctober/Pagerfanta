<?php

namespace Pagerfanta\Tests\View;

use Pagerfanta\View\ViewFactory;

class ViewFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $view1 = $this->getMockBuilder('Pagerfanta\View\ViewInterface')->getMock();
        $view2 = $this->getMockBuilder('Pagerfanta\View\ViewInterface')->getMock();
        $view3 = $this->getMockBuilder('Pagerfanta\View\ViewInterface')->getMock();
        $view4 = $this->getMockBuilder('Pagerfanta\View\ViewInterface')->getMock();

        $factory = new ViewFactory();

        $factory->set('foo', $view1);
        $factory->set('bar', $view2);

        $this->assertSame(array('foo' => $view1, 'bar' => $view2), $factory->all());

        $this->assertSame($view1, $factory->get('foo'));
        $this->assertSame($view2, $factory->get('bar'));
        try {
            $factory->get('foobar');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Pagerfanta\Exception\InvalidArgumentException', $e);
        }

        $this->assertTrue($factory->has('foo'));
        $this->assertTrue($factory->has('bar'));
        $this->assertFalse($factory->has('foobar'));

        $factory->add(array(
            'ups' => $view3,
            'man' => $view4,
        ));
        $this->assertSame($view3, $factory->get('ups'));
        $this->assertSame($view4, $factory->get('man'));
        $this->assertTrue($factory->has('ups'));
        $this->assertTrue($factory->has('man'));
        $this->assertSame(array(
            'foo' => $view1,
            'bar' => $view2,
            'ups' => $view3,
            'man' => $view4,
        ), $factory->all());

        $factory->remove('bar');
        $this->assertFalse($factory->has('bar'));
        $this->assertTrue($factory->has('foo'));
        $this->assertTrue($factory->has('ups'));
        $this->assertTrue($factory->has('man'));
        $this->assertSame(array(
            'foo' => $view1,
            'ups' => $view3,
            'man' => $view4,
        ), $factory->all());
        try {
            $factory->remove('foobar');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Pagerfanta\Exception\InvalidArgumentException', $e);
        }

        $factory->clear();
        $this->assertSame(array(), $factory->all());
    }
}
