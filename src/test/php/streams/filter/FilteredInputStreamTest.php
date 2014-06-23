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
 * Test for stubbles\streams\filter\FilteredInputStream.
 *
 * @group  streams
 * @group  streams_filter
 */
class FilteredInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FilteredInputStream
     */
    private $filteredInputStream;
    /**
     * mocked input stream
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInputStream;
    /**
     * mocked stream filter
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockStreamFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInputStream     = $this->getMock('stubbles\streams\InputStream');
        $this->mockStreamFilter    = $this->getMock('stubbles\streams\filter\StreamFilter');
        $this->filteredInputStream = new FilteredInputStream($this->mockInputStream, $this->mockStreamFilter);
    }

    /**
     * data returned from read() should be filtered
     *
     * @test
     */
    public function readAndFilter()
    {
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('eof')
                              ->will($this->onConsecutiveCalls(false, false));
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('read')
                              ->with($this->equalTo(8192))
                              ->will($this->onConsecutiveCalls('foo', 'bar'));
        $this->mockStreamFilter->expects($this->exactly(2))
                               ->method('shouldFilter')
                               ->will($this->onConsecutiveCalls(true, false));
        $this->assertEquals('bar', $this->filteredInputStream->read());
    }

    /**
     * data returned from read() should be filtered
     *
     * @test
     */
    public function readAndFilterUntilEnd()
    {
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('eof')
                              ->will($this->onConsecutiveCalls(false, true));
        $this->mockInputStream->expects($this->once())
                              ->method('read')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue('foo'));
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(true));
        $this->assertEquals('', $this->filteredInputStream->read());
    }

    /**
     * data returned from readLine() should be filtered
     *
     * @test
     */
    public function readLineAndFilter()
    {
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('eof')
                              ->will($this->onConsecutiveCalls(false, false));
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('readLine')
                              ->with($this->equalTo(8192))
                              ->will($this->onConsecutiveCalls('foo', 'bar'));
        $this->mockStreamFilter->expects($this->exactly(2))
                               ->method('shouldFilter')
                               ->will($this->onConsecutiveCalls(true, false));
        $this->assertEquals('bar', $this->filteredInputStream->readLine());
    }

    /**
     * data returned from readLine() should be filtered
     *
     * @test
     */
    public function readLineAndFilterUntilEnd()
    {
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('eof')
                              ->will($this->onConsecutiveCalls(false, true));
        $this->mockInputStream->expects($this->once())
                              ->method('readLine')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue('foo'));
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(true));
        $this->assertEquals('', $this->filteredInputStream->readLine());
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function canCreateInstanceWithCallableAsStreamFilter()
    {
        $callable = function($data)
                    {
                        $this->assertEquals('foo', $data);
                        return true;
                    };
        $this->mockInputStream->expects($this->exactly(2))
                              ->method('eof')
                              ->will($this->onConsecutiveCalls(false, true));
        $this->mockInputStream->expects($this->once())
                              ->method('readLine')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue('foo'));
        $this->filteredInputStream = new FilteredInputStream($this->mockInputStream, $callable);
        $this->assertEquals('', $this->filteredInputStream->readLine());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     * @since  4.0.0
     */
    public function createInstanceWithNoStreamFilterAndNoCallableThrowsIllegalArgumentException()
    {
        new FilteredInputStream($this->mockInputStream, new \stdClass());
    }
}
