<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams;
/**
 * Helper class for the test to make abstract class instantiable.
 */
class TestAbstractDecoratedInputStream extends AbstractDecoratedInputStream
{
    // intentionally empty
}
/**
 * Test for stubbles\streams\AbstractDecoratedInputStream.
 *
 * @group  streams
 */
class AbstractDecoratedInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AbstractDecoratedInputStream
     */
    protected $abstractDecoratedInputStream;
    /**
     * mocked input stream
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInputStream              = $this->getMock('stubbles\streams\InputStream');
        $this->abstractDecoratedInputStream = new TestAbstractDecoratedInputStream($this->mockInputStream);
    }

    /**
     * @test
     */
    public function readCallsDecoratedStream()
    {
        $this->mockInputStream->method('read')
                ->with(equalTo(8192))
                ->will(returnValue('foo'));
        $this->assertEquals('foo', $this->abstractDecoratedInputStream->read());
    }

    /**
     * @test
     */
    public function readLineCallsDecoratedStream()
    {
        $this->mockInputStream->method('readLine')
                ->with(equalTo(8192))
                ->will(returnValue('foo'));
        $this->assertEquals('foo', $this->abstractDecoratedInputStream->readLine());
    }

    /**
     * @test
     */
    public function bytesLeftCallsDecoratedStream()
    {
        $this->mockInputStream->method('bytesLeft')->will(returnValue(5));
        $this->assertEquals(5, $this->abstractDecoratedInputStream->bytesLeft());
    }

    /**
     * @test
     */
    public function eofCallsDecoratedStream()
    {
        $this->mockInputStream->method('eof')->will(returnValue(false));
        $this->assertFalse($this->abstractDecoratedInputStream->eof());
    }

    /**
     * @test
     */
    public function closeCallsDecoratedStream()
    {
        $this->mockInputStream->expects(once())->method('close');
        $this->abstractDecoratedInputStream->close();
    }
}
