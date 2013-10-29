<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams;
/**
 * Helper class for the test to make abstract class instantiable.
 */
class TestAbstractDecoratedOutputStream extends AbstractDecoratedOutputStream
{
    // intentionally empty
}
/**
 * Test for net\stubbles\streams\AbstractDecoratedOutputStream.
 *
 * @group streams
 */
class AbstractDecoratedOutputStreamTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AbstractDecoratedOutputStream
     */
    protected $abstractDecoratedOutputStream;
    /**
     * mocked input stream
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockOutputStream              = $this->getMock('net\\stubbles\\streams\\OutputStream');
        $this->abstractDecoratedOutputStream = new TestAbstractDecoratedOutputStream($this->mockOutputStream);
    }

    /**
     * @test
     */
    public function writeCallsDecoratedStream()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('write')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(3));
        $this->assertEquals(3, $this->abstractDecoratedOutputStream->write('foo'));
    }

    /**
     * @test
     */
    public function writeLineCallsDecoratedStream()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(4));
        $this->assertEquals(4, $this->abstractDecoratedOutputStream->writeLine('foo'));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesCallsDecoratedStream()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLines')
                               ->with($this->equalTo(array('foo', 'bar')))
                               ->will($this->returnValue(8));
        $this->assertEquals(8, $this->abstractDecoratedOutputStream->writeLines(array('foo', 'bar')));
    }

    /**
     * @test
     */
    public function closeCallsDecoratedStream()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('close');
        $this->abstractDecoratedOutputStream->close();
    }
}
