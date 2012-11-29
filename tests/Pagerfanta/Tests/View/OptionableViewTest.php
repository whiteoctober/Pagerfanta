<?php

namespace Pagerfanta\Tests\View;

use Pagerfanta\View\OptionableView;

class OptionableViewTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $routeGenerator = function($page, $route = '') {
            return '/'.$page;
        };
        $pagerfanta = $this->getMock('Pagerfanta\PagerfantaInterface');

        $view = $this->getMock('Pagerfanta\View\ViewInterface');
        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($pagerfanta),
                $this->equalTo($routeGenerator),
                $this->equalTo(array('option1' => 'foo', 'option2' => 'ups'))
            )
            ->will($this->returnValue($rendered = '<nav>...</nav>'))
        ;

        $optionable = new OptionableView($view, array('option1' => 'foo', 'option2' => 'bar'));
        $this->assertSame($rendered, $optionable->render($pagerfanta, $routeGenerator, array('option2' => 'ups')));
    }
}
