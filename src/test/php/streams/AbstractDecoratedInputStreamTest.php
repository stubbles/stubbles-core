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
use stubbles\streams\memory\MemoryInputStream;
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
     * @type  \stubbles\streams\AbstractDecoratedInputStream
     */
    private $abstractDecoratedInputStream;
    /**
     * mocked input stream
     *
     * @type  \stubbles\streams\memory\MemoryInputStream
     */
    private $memory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memory = new MemoryInputStream("foo\n");
        $this->abstractDecoratedInputStream = new TestAbstractDecoratedInputStream($this->memory);
    }

    /**
     * @test
     */
    public function readCallsDecoratedStream()
    {
        $this->assertEquals("foo\n", $this->abstractDecoratedInputStream->read());
    }

    /**
     * @test
     */
    public function readLineCallsDecoratedStream()
    {
        $this->assertEquals('foo', $this->abstractDecoratedInputStream->readLine());
    }

    /**
     * @test
     */
    public function bytesLeftCallsDecoratedStream()
    {
        $this->assertEquals(4, $this->abstractDecoratedInputStream->bytesLeft());
    }

    /**
     * @test
     */
    public function eofCallsDecoratedStream()
    {
        $this->assertFalse($this->abstractDecoratedInputStream->eof());
    }

    /**
     * @test
     */
    public function closeCallsDecoratedStream()
    {
        $mockInputStream = $this->getMock('stubbles\streams\InputStream');
        $mockInputStream->expects(once())->method('close');
        $abstractDecoratedInputStream = new TestAbstractDecoratedInputStream($mockInputStream);
        $abstractDecoratedInputStream->close();
    }
}
