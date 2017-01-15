<?php

namespace Pagerfanta\Tests\View;

use Pagerfanta\View\OptionableView;

class OptionableViewTest extends \PHPUnit_Framework_TestCase
{
    private $pagerfanta;
    private $routeGenerator;
    private $rendered;

    protected function setUp()
    {
        $this->pagerfanta = $this->createPagerfantaMock();
        $this->routeGenerator = $this->createRouteGenerator();
    }

    private function createPagerfantaMock()
    {
        return $this->getMockBuilder('Pagerfanta\PagerfantaInterface')->getMock();
    }

    private function createRouteGenerator()
    {
        return function () {};
    }

    public function testRenderShouldDelegateToTheView()
    {
        $defaultOptions = array('foo' => 'bar', 'bar' => 'ups');

        $view = $this->createViewMock($defaultOptions);
        $optionable = new OptionableView($view, $defaultOptions);

        $returned = $optionable->render($this->pagerfanta, $this->routeGenerator);
        $this->assertSame($this->rendered, $returned);
    }

    public function testRenderShouldMergeOptions()
    {
        $defaultOptions = array('foo' => 'bar');
        $options = array('ups' => 'da');
        $expectedOptions = array_merge($defaultOptions, $options);

        $view = $this->createViewMock($expectedOptions);
        $optionable = new OptionableView($view, $defaultOptions);

        $returned = $optionable->render($this->pagerfanta, $this->routeGenerator, $options);
        $this->assertSame($this->rendered, $returned);
    }

    private function createViewMock($expectedOptions)
    {
        $view = $this->getMockBuilder('Pagerfanta\View\ViewInterface')->getMock();
        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($this->pagerfanta),
                $this->equalTo($this->routeGenerator),
                $this->equalTo($expectedOptions)
            )
            ->will($this->returnValue($this->rendered));

        return $view;
    }
}
