<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\filter;
/**
 * Test for stubbles\streams\filter\FilteredOutputStream.
 *
 * @group  streams
 * @group  streams_filter
 */
class FilteredOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FilteredOutputStream
     */
    private $filteredOutputStream;
    /**
     * mocked input stream
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOutputStream;
    /**
     * mocked stream filter
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockPredicate;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockOutputStream     = $this->getMock('stubbles\streams\OutputStream');
        $this->mockPredicate        = $this->getMock('stubbles\predicate\Predicate');
        $this->filteredOutputStream = new FilteredOutputStream($this->mockOutputStream, $this->mockPredicate);
    }

    /**
     * @test
     */
    public function dataPassingTheFilterShouldBeWritten()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('write')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(3));
        $this->mockPredicate->expects($this->once())
                            ->method('test')
                            ->will($this->returnValue(true));
        $this->assertEquals(3, $this->filteredOutputStream->write('foo'));
    }

    /**
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWritten()
    {
        $this->mockOutputStream->expects($this->never())
                               ->method('write');
        $this->mockPredicate->expects($this->once())
                            ->method('test')
                            ->will($this->returnValue(false));
        $this->assertEquals(0, $this->filteredOutputStream->write('foo'));
    }

    /**
     * @test
     */
    public function dataPassingTheFilterShouldBeWrittenAsLine()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(3));
        $this->mockPredicate->expects($this->once())
                            ->method('test')
                            ->will($this->returnValue(true));
        $this->assertEquals(3, $this->filteredOutputStream->writeLine('foo'));
    }

    /**
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWrittenAsLine()
    {
        $this->mockOutputStream->expects($this->never())
                               ->method('writeLine');
        $this->mockPredicate->expects($this->once())
                            ->method('test')
                            ->will($this->returnValue(false));
        $this->assertEquals(0, $this->filteredOutputStream->writeLine('foo'));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesProcessesOnlyLinesSatisfyingFilter()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(3));
        $this->mockPredicate->expects($this->exactly(2))
                            ->method('test')
                            ->will($this->onConsecutiveCalls(true, false));
        $this->assertEquals(3, $this->filteredOutputStream->writeLines(['foo', 'bar']));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  4.0.0
     */
    public function createInstanceWithNoStreamFilterAndNoPredicateAndNoCallableThrowsIllegalArgumentException()
    {
        new FilteredOutputStream($this->mockOutputStream, new \stdClass());
    }
}
