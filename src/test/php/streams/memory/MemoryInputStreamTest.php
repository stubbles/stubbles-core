<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\memory;
use stubbles\streams\Seekable;
/**
 * Test for stubbles\streams\memory\MemoryInputStream.
 *
 * @group  streams
 * @group  streams_memory
 */
class MemoryInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * the file url used in the tests
     *
     * @type  MemoryInputStream
     */
    protected $memoryInputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryInputStream = new MemoryInputStream("hello\nworld");
    }

    /**
     * @test
     */
    public function read()
    {
        assertFalse($this->memoryInputStream->eof());
        assertEquals(11, $this->memoryInputStream->bytesLeft());
        assertEquals(0, $this->memoryInputStream->tell());
        assertEquals("hello\nworld", $this->memoryInputStream->read());
        assertTrue($this->memoryInputStream->eof());
        assertEquals(0, $this->memoryInputStream->bytesLeft());
        assertEquals(11, $this->memoryInputStream->tell());
    }

    /**
     * @test
     */
    public function readLineSplitsOnLineBreak()
    {
        assertFalse($this->memoryInputStream->eof());
        assertEquals(11, $this->memoryInputStream->bytesLeft());
        assertEquals(0, $this->memoryInputStream->tell());
        assertEquals('hello', $this->memoryInputStream->readLine());
        assertFalse($this->memoryInputStream->eof());
        assertEquals(5, $this->memoryInputStream->bytesLeft());
        assertEquals(6, $this->memoryInputStream->tell());
        assertEquals('world', $this->memoryInputStream->readLine());
        assertTrue($this->memoryInputStream->eof());
        assertEquals(0, $this->memoryInputStream->bytesLeft());
        assertEquals(11, $this->memoryInputStream->tell());
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function readLineWithBothLineBreaks()
    {
        $this->memoryInputStream = new MemoryInputStream("hello\r\nworld");
        assertFalse($this->memoryInputStream->eof());
        assertEquals(12, $this->memoryInputStream->bytesLeft());
        assertEquals(0, $this->memoryInputStream->tell());
        assertEquals('hello', $this->memoryInputStream->readLine());
        assertFalse($this->memoryInputStream->eof());
        assertEquals(5, $this->memoryInputStream->bytesLeft());
        assertEquals(7, $this->memoryInputStream->tell());
        assertEquals('world', $this->memoryInputStream->readLine());
        assertTrue($this->memoryInputStream->eof());
        assertEquals(0, $this->memoryInputStream->bytesLeft());
        assertEquals(12, $this->memoryInputStream->tell());
    }

    /**
     * @test
     */
    public function closeDoesNothing()
    {
        assertNull($this->memoryInputStream->close());
    }

    /**
     * @test
     */
    public function seekCanSetPosition()
    {
        $this->memoryInputStream->seek(6);
        assertEquals(6, $this->memoryInputStream->tell());
        assertEquals(5, $this->memoryInputStream->bytesLeft());
        assertFalse($this->memoryInputStream->eof());
        assertEquals('world', $this->memoryInputStream->read());
        $this->memoryInputStream->seek(0, Seekable::SET);
        assertEquals(11, $this->memoryInputStream->bytesLeft());
        assertEquals(0, $this->memoryInputStream->tell());
        assertEquals("hello\nworld", $this->memoryInputStream->read());
    }

    /**
     * seek() sets position of of buffer
     *
     * @test
     */
    public function seekCanSetPositionFromCurrentPosition()
    {
        $this->memoryInputStream->read(4);
        $this->memoryInputStream->seek(2, Seekable::CURRENT);
        assertEquals(6, $this->memoryInputStream->tell());
        assertEquals(5, $this->memoryInputStream->bytesLeft());
        assertFalse($this->memoryInputStream->eof());
        assertEquals('world', $this->memoryInputStream->read());
    }

    /**
     * @test
     */
    public function seekCanSetPositionFromEnd()
    {
        $this->memoryInputStream->seek(-5, Seekable::END);
        assertEquals(6, $this->memoryInputStream->tell());
        assertEquals(5, $this->memoryInputStream->bytesLeft());
        assertFalse($this->memoryInputStream->eof());
        assertEquals('world', $this->memoryInputStream->read());
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function seekThrowsIllegalArgumentExceptionForInvalidWhence()
    {
        $this->memoryInputStream->seek(6, 66);
    }
}
