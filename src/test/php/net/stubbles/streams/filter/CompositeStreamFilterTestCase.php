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
 * Test for net\stubbles\streams\filter\CompositeStreamFilter.
 *
 * @group  streams
 * @group  streams_filter
 */
class CompositeStreamFilterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  CompositeStreamFilter
     */
    protected $compositeStreamFilter;
    /**
     * mocked stream filter
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStreamFilter1;
    /**
     * mocked stream filter
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStreamFilter2;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockStreamFilter1     = $this->getMock('net\\stubbles\\streams\\filter\\StreamFilter');
        $this->mockStreamFilter2     = $this->getMock('net\\stubbles\\streams\\filter\\StreamFilter');
        $this->compositeStreamFilter = new CompositeStreamFilter();
        $this->compositeStreamFilter->addStreamFilter($this->mockStreamFilter1)
                                    ->addStreamFilter($this->mockStreamFilter2);
    }

    /**
     * @test
     */
    public function returnsFalseIfNoFilterAdded()
    {
        $this->compositeStreamFilter = new CompositeStreamFilter();
        $this->assertFalse($this->compositeStreamFilter->shouldFilter('foo'));
    }

    /**
     * @test
     */
    public function noFilterAppliesReturnsFalse()
    {
        $this->mockStreamFilter1->expects($this->once())
                                ->method('shouldFilter')
                                ->with($this->equalTo('foo'))
                                ->will($this->returnValue(false));
        $this->mockStreamFilter2->expects($this->once())
                                ->method('shouldFilter')
                                ->with($this->equalTo('foo'))
                                ->will($this->returnValue(false));
        $this->assertFalse($this->compositeStreamFilter->shouldFilter('foo'));
    }

    /**
     * @test
     */
    public function filterAppliesReturnsTrue()
    {
        $this->mockStreamFilter1->expects($this->once())
                                ->method('shouldFilter')
                                ->with($this->equalTo('foo'))
                                ->will($this->returnValue(true));
        $this->mockStreamFilter2->expects($this->never())
                                ->method('shouldFilter');
        $this->assertTrue($this->compositeStreamFilter->shouldFilter('foo'));
    }
}
?>