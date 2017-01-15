<?php

namespace Pagerfanta\Tests\View;

use Pagerfanta\Pagerfanta;
use Pagerfanta\View\ViewInterface;

abstract class ViewTestCase extends \PHPUnit_Framework_TestCase
{
    private $adapter;
    /**
     * @var Pagerfanta
     */
    private $pagerfanta;
    /**
     * @var ViewInterface
     */
    private $view;

    protected function setUp()
    {
        $this->adapter = $this->createAdapterMock();
        $this->pagerfanta = new Pagerfanta($this->adapter);
        $this->view = $this->createView();
    }

    private function createAdapterMock()
    {
        return $this->getMockBuilder('Pagerfanta\Adapter\AdapterInterface')->getMock();
    }

    /**
     * @return ViewInterface
     */
    abstract protected function createView();

    protected function setNbPages($nbPages)
    {
        $nbResults = $this->calculateNbResults($nbPages);

        $this->adapter
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue($nbResults));
    }

    private function calculateNbResults($nbPages)
    {
        return $nbPages * $this->pagerfanta->getMaxPerPage();
    }

    protected function setCurrentPage($currentPage)
    {
        $this->pagerfanta->setCurrentPage($currentPage);
    }

    protected function renderView($options)
    {
        $routeGenerator = $this->createRouteGenerator();

        return $this->view->render($this->pagerfanta, $routeGenerator, $options);
    }

    protected function createRouteGenerator()
    {
        return function ($page) { return '|'.$page.'|'; };
    }

    protected function assertRenderedView($expected, $result)
    {
        $this->assertSame($this->filterExpectedView($expected), $result);
    }

    protected function filterExpectedView($expected)
    {
        return $expected;
    }

    protected function removeWhitespacesBetweenTags($string)
    {
        return preg_replace('/>\s+</', '><', $string);
    }
}
