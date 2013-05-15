<?php

namespace Pagerfanta\Tests;

use Pagerfanta\Pagerfanta;

class IteratorAggregate implements \IteratorAggregate
{
    private $iterator;

    public function __construct()
    {
        $this->iterator = new \ArrayIterator(array('ups'));
    }

    public function getIterator()
    {
        return $this->iterator;
    }
}

class PagerfantaTest extends \PHPUnit_Framework_TestCase
{
    private $adapter;
    private $pagerfanta;

    protected function setUp()
    {
        $this->adapter = $this->getMock('Pagerfanta\Adapter\AdapterInterface');
        $this->pagerfanta = new Pagerfanta($this->adapter);
    }

    private function setAdapterNbResultsAny($nbResults)
    {
        $this->setAdapterNbResults($this->any(), $nbResults);
    }

    private function setAdapterNbResultsOnce($nbResults)
    {
        $this->setAdapterNbResults($this->once(), $nbResults);
    }

    private function setAdapterNbResults($expects, $nbResults)
    {
        $this->adapter
            ->expects($expects)
            ->method('getNbResults')
            ->will($this->returnValue($nbResults));
    }

    public function testGetAdapterShouldReturnTheAdapter()
    {
        $this->assertSame($this->adapter, $this->pagerfanta->getAdapter());
    }

    public function testGetAllowOutOfRangePagesShouldBeFalseByDefault()
    {
        $this->assertFalse($this->pagerfanta->getAllowOutOfRangePages());
    }

    public function testSetAllowOutOfRangePagesShouldSetTrue()
    {
        $this->pagerfanta->setAllowOutOfRangePages(true);
        $this->assertTrue($this->pagerfanta->getAllowOutOfRangePages());
    }

    public function testSetAllowOutOfRangePagesShouldSetFalse()
    {
        $this->pagerfanta->setAllowOutOfRangePages(false);
        $this->assertFalse($this->pagerfanta->getAllowOutOfRangePages());
    }

    public function testSetAllowOutOfRangePagesShouldReturnThePagerfanta()
    {
        $this->assertSame($this->pagerfanta, $this->pagerfanta->setAllowOutOfRangePages(true));
    }

    /**
     * @expectedException Pagerfanta\Exception\NotBooleanException
     * @dataProvider notBooleanProvider
     */
    public function testSetAllowOutOfRangePagesShouldThrowNotBooleanExceptionWhenNotBoolean($value)
    {
        $this->pagerfanta->setAllowOutOfRangePages($value);
    }

    public function testGetNormalizeOutOfRangePagesShouldBeFalseByDefault()
    {
        $this->assertFalse($this->pagerfanta->getNormalizeOutOfRangePages());
    }

    public function testSetNormalizeOutOfRangePagesShouldSetTrue()
    {
        $this->pagerfanta->setNormalizeOutOfRangePages(true);
        $this->assertTrue($this->pagerfanta->getNormalizeOutOfRangePages());
    }

    public function testSetNormalizeOutOfRangePagesShouldSetFalse()
    {
        $this->pagerfanta->setNormalizeOutOfRangePages(false);
        $this->assertFalse($this->pagerfanta->getNormalizeOutOfRangePages());
    }

    public function testSetNormalizeOutOfRangePagesShouldReturnThePagerfanta()
    {
        $this->assertSame($this->pagerfanta, $this->pagerfanta->setNormalizeOutOfRangePages(true));
    }

    /**
     * @expectedException Pagerfanta\Exception\NotBooleanException
     * @dataProvider notBooleanProvider
     */
    public function testSetNormalizeOutOfRangePagesShouldThrowNotBooleanExceptionWhenNotBoolean($value)
    {
        $this->pagerfanta->setNormalizeOutOfRangePages($value);
    }

    public function notBooleanProvider()
    {
        return array(
            array(1),
            array('1'),
            array(1.1),
        );
    }

    /**
     * @dataProvider setMaxPerPageShouldSetAnIntegerProvider
     */
    public function testSetMaxPerPageShouldSetAnInteger($maxPerPage)
    {
        $this->pagerfanta->setMaxPerPage($maxPerPage);

        $this->assertSame($maxPerPage, $this->pagerfanta->getMaxPerPage());
    }

    public function setMaxPerPageShouldSetAnIntegerProvider()
    {
        return array(
            array(1),
            array(10),
            array(25),
        );
    }

    /**
     * @dataProvider setMaxPerPageShouldConvertStringsToIntegersProvider
     */
    public function testSetMaxPerPageShouldConvertStringsToIntegers($maxPerPage)
    {
        $this->pagerfanta->setMaxPerPage($maxPerPage);
        $this->assertSame((int) $maxPerPage, $this->pagerfanta->getMaxPerPage());
    }

    public function setMaxPerPageShouldConvertStringsToIntegersProvider()
    {
        return array(
            array('1'),
            array('10'),
            array('25'),
        );
    }

    public function testSetMaxPerPageShouldReturnThePagerfanta()
    {
        $this->assertSame($this->pagerfanta, $this->pagerfanta->setMaxPerPage(10));
    }

    /**
     * @expectedException Pagerfanta\Exception\NotIntegerMaxPerPageException
     * @dataProvider      setMaxPerPageShouldThrowExceptionWhenInvalidProvider
     */
    public function testSetMaxPerPageShouldThrowExceptionWhenInvalid($maxPerPage)
    {
        $this->pagerfanta->setMaxPerPage($maxPerPage);
    }

    public function setMaxPerPageShouldThrowExceptionWhenInvalidProvider()
    {
        return array(
            array(1.1),
            array('1.1'),
            array(true),
            array(array(1)),
        );
    }

    /**
     * @expectedException Pagerfanta\Exception\LessThan1MaxPerPageException
     * @dataProvider      setMaxPerPageShouldThrowExceptionWhenLessThan1Provider
     */
    public function testSetMaxPerPageShouldThrowExceptionWhenLessThan1($maxPerPage)
    {
        $this->pagerfanta->setMaxPerPage($maxPerPage);
    }

    public function setMaxPerPageShouldThrowExceptionWhenLessThan1Provider()
    {
        return array(
            array(0),
            array(-1),
        );
    }

    public function testSetMaxPerPageShouldResetCurrentPageResults()
    {
        $pagerfanta = $this->pagerfanta;

        $this->assertResetCurrentPageResults(function () use ($pagerfanta) {
            $pagerfanta->setMaxPerPage(10);
        });
    }

    public function testSetMaxPerPageShouldResetNbResults()
    {
        $this->prepareForResetNbResults();

        $this->assertSame(100, $this->pagerfanta->getNbResults());
        $this->pagerfanta->setMaxPerPage(10);
        $this->assertSame(50, $this->pagerfanta->getNbResults());
    }

    public function testSetMaxPerPageShouldResetNbPages()
    {
        $this->prepareForResetNbResults();

        $this->assertSame(10, $this->pagerfanta->getNbPages());
        $this->pagerfanta->setMaxPerPage(10);
        $this->assertSame(5, $this->pagerfanta->getNbPages());
    }

    private function prepareForResetNbResults()
    {
        $this->pagerfanta->setMaxPerPage(10);

        $this->adapter
            ->expects($this->at(0))
            ->method('getNbResults')
            ->will($this->returnValue(100));
        $this->adapter
            ->expects($this->at(1))
            ->method('getNbResults')
            ->will($this->returnValue(50));
    }

    public function testGetNbResultsShouldReturnTheNbResultsFromTheAdapter()
    {
        $this->setAdapterNbResultsAny(20);

        $this->assertSame(20, $this->pagerfanta->getNbResults());
    }

    public function testGetNbResultsShouldCacheTheNbResultsFromTheAdapter()
    {
        $this->setAdapterNbResultsOnce(20);

        $this->pagerfanta->getNbResults();
        $this->pagerfanta->getNbResults();
    }

    public function testGetNbPagesShouldCalculateTheNumberOfPages()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(20);

        $this->assertSame(5, $this->pagerfanta->getNbPages());
    }

    public function testGetNbPagesShouldRoundToUp()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(15);

        $this->assertSame(7, $this->pagerfanta->getNbPages());
    }

    public function testGetNbPagesShouldReturn1WhenThereAreNoResults()
    {
        $this->setAdapterNbResultsAny(0);

        $this->assertSame(1, $this->pagerfanta->getNbPages());
    }

    /**
     * @dataProvider setCurrentPageShouldSetAnIntegerProvider
     */
    public function testSetCurrentPageShouldSetAnInteger($currentPage)
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(2);
        $this->pagerfanta->setCurrentPage($currentPage);

        $this->assertSame($currentPage, $this->pagerfanta->getCurrentPage());
    }

    public function setCurrentPageShouldSetAnIntegerProvider()
    {
        return array(
            array(1),
            array(10),
            array(25),
        );
    }

    /**
     * @dataProvider setCurrentPageShouldConvertStringsToIntegersProvider
     */
    public function testSetCurrentPageShouldConvertStringsToIntegers($currentPage)
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(2);
        $this->pagerfanta->setCurrentPage($currentPage);

        $this->assertSame((int) $currentPage, $this->pagerfanta->getCurrentPage());
    }

    public function setCurrentPageShouldConvertStringsToIntegersProvider()
    {
        return array(
            array('1'),
            array('10'),
            array('25'),
        );
    }

    /**
     * @expectedException Pagerfanta\Exception\NotIntegerCurrentPageException
     * @dataProvider      setCurrentPageShouldThrowExceptionWhenInvalidProvider
     */
    public function testSetCurrentPageShouldThrowExceptionWhenInvalid($currentPage)
    {
        $this->pagerfanta->setCurrentPage($currentPage);
    }

    public function setCurrentPageShouldThrowExceptionWhenInvalidProvider()
    {
        return array(
            array(1.1),
            array('1.1'),
            array(true),
            array(array(1)),
        );
    }

    /**
     * @expectedException Pagerfanta\Exception\LessThan1CurrentPageException
     * @dataProvider      setCurrentPageShouldThrowExceptionWhenLessThan1Provider
     */
    public function testCurrentPagePageShouldThrowExceptionWhenLessThan1($currentPage)
    {
        $this->pagerfanta->setCurrentPage($currentPage);
    }

    public function setCurrentPageShouldThrowExceptionWhenLessThan1Provider()
    {
        return array(
            array(0),
            array(-1),
        );
    }

    /**
     * @expectedException Pagerfanta\Exception\OutOfRangeCurrentPageException
     */
    public function testSetCurrentPageShouldThrowExceptionWhenThePageIsOutOfRange()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setCurrentPage(11);
    }

    public function testSetCurrentPageShouldNotThrowExceptionWhenIndicatingAllowOurOfRangePages()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setAllowOutOfRangePages(true);
        $this->pagerfanta->setCurrentPage(11);

        $this->assertSame(11, $this->pagerfanta->getCurrentPage());
    }

    public function testSetCurrentPageShouldNotThrowExceptionWhenIndicatingAllowOurOfRangePagesWithOldBooleanArguments()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setCurrentPage(11, true);

        $this->assertSame(11, $this->pagerfanta->getCurrentPage());
    }

    public function testSetCurrentPageShouldReturnThePagerfanta()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);

        $this->assertSame($this->pagerfanta, $this->pagerfanta->setCurrentPage(1));
    }

    public function testSetCurrentPageShouldNormalizePageWhenOutOfRangePageAndIndicatingNormalizeOutOfRangePages()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setAllowOutOfRangePages(false);
        $this->pagerfanta->setNormalizeOutOfRangePages(true);
        $this->pagerfanta->setCurrentPage(11);

        $this->assertSame(10, $this->pagerfanta->getCurrentPage());
    }

    public function testSetCurrentPageShouldNormalizePageWhenOutOfRangePageAndIndicatingNormalizeOutOfRangePagesWithDeprecatedBooleansArguments()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setCurrentPage(11, false, true);

        $this->assertSame(10, $this->pagerfanta->getCurrentPage());
    }

    public function testSetCurrentPageShouldResetCurrentPageResults()
    {
        $pagerfanta = $this->pagerfanta;

        $this->assertResetCurrentPageResults(function () use ($pagerfanta) {
            $pagerfanta->setCurrentPage(1);
        });
    }

    /**
     * @dataProvider testGetCurrentPageResultsShouldReturnASliceFromTheAdapterDependingOnTheCurrentPageAndMaxPerPageProvider
     */
    public function testGetCurrentPageResultsShouldReturnASliceFromTheAdapterDependingOnTheCurrentPageAndMaxPerPage($maxPerPage, $currentPage, $offset)
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage($maxPerPage);
        $this->pagerfanta->setCurrentPage($currentPage);

        $currentPageResults = new \ArrayObject();

        $this->adapter
            ->expects($this->any())
            ->method('getSlice')
            ->with($this->equalTo($offset), $this->equalTo($maxPerPage))
            ->will($this->returnValue($currentPageResults));

        $this->assertSame($currentPageResults, $this->pagerfanta->getCurrentPageResults());
    }

    public function testGetCurrentPageResultsShouldReturnASliceFromTheAdapterDependingOnTheCurrentPageAndMaxPerPageProvider()
    {
        // max per page, current page, offset
        return array(
            array(10, 1, 0),
            array(10, 2, 10),
            array(20, 3, 40),
        );
    }

    public function testGetCurrentPageResultsShouldCacheTheResults()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setCurrentPage(1);

        $currentPageResults = new \ArrayObject();

        $this->adapter
            ->expects($this->once())
            ->method('getSlice')
            ->will($this->returnValue($currentPageResults));

        $this->pagerfanta->getCurrentPageResults();
        $this->assertSame($currentPageResults, $this->pagerfanta->getCurrentPageResults());
    }

    public function testHaveToPaginateReturnsTrueWhenTheNumberOfResultsIsGreaterThanTheMaxPerPage()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(99);

        $this->assertTrue($this->pagerfanta->haveToPaginate());
    }

    public function testHaveToPaginateReturnsFalseWhenTheNumberOfResultsIsEqualToMaxPerPage()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(100);

        $this->assertFalse($this->pagerfanta->haveToPaginate());
    }

    public function testHaveToPaginateReturnsFalseWhenTheNumberOfResultsIsLessThanMaxPerPage()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(101);

        $this->assertFalse($this->pagerfanta->haveToPaginate());
    }

    public function testHasPreviousPageShouldReturnTrueWhenTheCurrentPageIsGreaterThan1()
    {
        $this->setAdapterNbResultsAny(100);

        foreach (array(2, 3) as $page) {
            $this->pagerfanta->setCurrentPage($page);
            $this->assertTrue($this->pagerfanta->hasPreviousPage());
        }
    }

    public function testHasPreviousPageShouldReturnFalseWhenTheCurrentPageIs1()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setCurrentPage(1);

        $this->assertFalse($this->pagerfanta->hasPreviousPage());
    }

    public function testGetPreviousPageShouldReturnThePreviousPage()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);

        foreach (array(2 => 1, 3 => 2) as $currentPage => $previousPage) {
            $this->pagerfanta->setCurrentPage($currentPage);
            $this->assertSame($previousPage, $this->pagerfanta->getPreviousPage());
        }

    }

    /**
     * @expectedException Pagerfanta\Exception\LogicException
     */
    public function testGetPreviousPageShouldThrowALogicExceptionIfThereIsNoPreviousPage()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setCurrentPage(1);

        $this->pagerfanta->getPreviousPage();
    }

    public function testHasNextPageShouldReturnTrueIfTheCurrentPageIsNotTheLast()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);

        foreach (array(1, 2) as $page) {
            $this->pagerfanta->setCurrentPage($page);
            $this->assertTrue($this->pagerfanta->hasNextPage());
        }
    }

    public function testHasNextPageShouldReturnFalseIfTheCurrentPageIsTheLast()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setCurrentPage(10);

        $this->assertFalse($this->pagerfanta->hasNextPage());
    }

    public function testGetNextPageShouldReturnTheNextPage()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);

        foreach (array(2 => 3, 3 => 4) as $currentPage => $nextPage) {
            $this->pagerfanta->setCurrentPage($currentPage);
            $this->assertSame($nextPage, $this->pagerfanta->getNextPage());
        }
    }

    /**
     * @expectedException Pagerfanta\Exception\LogicException
     */
    public function testGetNextPageShouldThrowALogicExceptionIfTheCurrentPageIsTheLast()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setCurrentPage(10);

        $this->pagerfanta->getNextPage();
    }

    public function testCountShouldReturnNbResults()
    {
        $this->setAdapterNbResultsAny(30);

        $this->assertSame(30, $this->pagerfanta->count());
    }

    public function testPagerfantaShouldImplementCountableInterface()
    {
        $this->assertInstanceOf('Countable', $this->pagerfanta);
    }

    public function testGetIteratorShouldReturnCurrentPageResultsIfItIsAnIterator()
    {
        $currentPageResults = new \ArrayIterator(array('foo'));
        $this->setAdapterGetSlice($currentPageResults);

        $expected = $currentPageResults;
        $this->assertSame($expected, $this->pagerfanta->getIterator());
    }

    public function testGetIteratorShouldReturnTheIteratorOfCurrentPageResultsIfItIsAnIteratorAggregate()
    {
        $currentPageResults = new IteratorAggregate();
        $this->setAdapterGetSlice($currentPageResults);

        $expected = $currentPageResults->getIterator();
        $this->assertSame($expected, $this->pagerfanta->getIterator());
    }

    public function testGetIteratorShouldReturnAnArrayIteratorIfCurrentPageResultsIsAnArray()
    {
        $currentPageResults = array('foo', 'bar');
        $this->setAdapterGetSlice($currentPageResults);

        $expected = new \ArrayIterator($currentPageResults);
        $this->assertEquals($expected, $this->pagerfanta->getIterator());
    }

    private function setAdapterGetSlice($currentPageResults)
    {
        $this->adapter
            ->expects($this->any())
            ->method('getSlice')
            ->will($this->returnValue($currentPageResults));
    }

    public function testPagerfantaShouldImplementIteratorAggregateInterface()
    {
        $this->assertInstanceOf('IteratorAggregate', $this->pagerfanta);
    }

    private function assertResetCurrentPageResults($callback)
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);

        $currentPageResults0 = new \ArrayObject();
        $currentPageResults1 = new \ArrayObject();

        $this->adapter
            ->expects($this->at(0))
            ->method('getSlice')
            ->will($this->returnValue($currentPageResults0));
        $this->adapter
            ->expects($this->at(1))
            ->method('getSlice')
            ->will($this->returnValue($currentPageResults1));

        $this->assertSame($currentPageResults0, $this->pagerfanta->getCurrentPageResults());
        $callback();
        $this->assertSame($currentPageResults1, $this->pagerfanta->getCurrentPageResults());
    }


    public function testGetCurrentPageOffsetStart()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setCurrentPage(2);

        $this->assertSame(11, $this->pagerfanta->getCurrentPageOffsetStart());
    }

    public function testGetCurrentPageOffsetEnd()
    {
        $this->setAdapterNbResultsAny(100);
        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setCurrentPage(2);

        $this->assertSame(20, $this->pagerfanta->getCurrentPageOffsetEnd());
    }

    public function testGetCurrentPageOffsetEndOnEndPage()
    {
        $this->setAdapterNbResultsAny(90);
        $this->pagerfanta->setMaxPerPage(20);
        $this->pagerfanta->setCurrentPage(5);

        $this->assertSame(90, $this->pagerfanta->getCurrentPageOffsetEnd());
    }

}
