<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams\filter;
/**
 * Test for net\stubbles\streams\filter\FilteredOutputStream.
 *
 * @group  streams
 * @group  streams_filter
 */
class FilteredOutputStreamTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FilteredOutputStream
     */
    protected $filteredOutputStream;
    /**
     * mocked input stream
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockOutputStream;
    /**
     * mocked stream filter
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStreamFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockOutputStream     = $this->getMock('net\\stubbles\\streams\\OutputStream');
        $this->mockStreamFilter     = $this->getMock('net\\stubbles\\streams\\filter\\StreamFilter');
        $this->filteredOutputStream = new FilteredOutputStream($this->mockOutputStream, $this->mockStreamFilter);
    }

    /**
     * data passing the filter should be written
     *
     * @test
     */
    public function dataPassingTheFilterShouldBeWritten()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('write')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(3));
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(false));
        $this->assertEquals(3, $this->filteredOutputStream->write('foo'));
    }

    /**
     * data passing the filter should be written
     *
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWritten()
    {
        $this->mockOutputStream->expects($this->never())
                               ->method('write');
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(true));
        $this->assertEquals(0, $this->filteredOutputStream->write('foo'));
    }

    /**
     * data passing the filter should be written
     *
     * @test
     */
    public function dataPassingTheFilterShouldBeWrittenAsLine()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(3));
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(false));
        $this->assertEquals(3, $this->filteredOutputStream->writeLine('foo'));
    }

    /**
     * data passing the filter should be written
     *
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWrittenAsLine()
    {
        $this->mockOutputStream->expects($this->never())
                               ->method('writeLine');
        $this->mockStreamFilter->expects($this->once())
                               ->method('shouldFilter')
                               ->will($this->returnValue(true));
        $this->assertEquals(0, $this->filteredOutputStream->writeLine('foo'));
    }
}
?>