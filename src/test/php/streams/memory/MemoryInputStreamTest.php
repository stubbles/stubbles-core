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

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
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
    public function isNotAtEofWhenBytesLeft()
    {
        assertFalse($this->memoryInputStream->eof());
    }

    /**
     * @test
     */
    public function returnsAmountOfBytesLeft()
    {
        assert($this->memoryInputStream->bytesLeft(), equals(11));
    }

    /**
     * @test
     */
    public function pointerIsAtBeginningAfterConstruction()
    {
        assert($this->memoryInputStream->tell(), equals(0));
    }

    /**
     * @test
     */
    public function readReturnsBytes()
    {
        assert($this->memoryInputStream->read(), equals("hello\nworld"));
    }

    /**
     * @test
     */
    public function hasReachedEofWhenEverythingWasRead()
    {
        $this->memoryInputStream->read();
        assertTrue($this->memoryInputStream->eof());
    }

    /**
     * @test
     */
    public function hasRNoBytesLeftWhenEverythingWasRead()
    {
        $this->memoryInputStream->read();
        assertEquals(0, $this->memoryInputStream->bytesLeft());
    }

    /**
     * @test
     */
    public function pointerIsAtLastPositionWhenEverythingWasRead()
    {
        $this->memoryInputStream->read();
        assertEquals(11, $this->memoryInputStream->tell());
    }

    /**
     * @test
     */
    public function readLineSplitsOnLineBreak()
    {
        assert($this->memoryInputStream->readLine(), equals('hello'));
    }

    /**
     * @test
     */
    public function isNotAtEndWhenOneLineOfSeveralRead()
    {
        $this->memoryInputStream->readLine();
        assertFalse($this->memoryInputStream->eof());
    }

    /**
     * @test
     */
    public function hasBytesLeftWhenOneLineOfServeralRead()
    {
        $this->memoryInputStream->readLine();
        assert($this->memoryInputStream->bytesLeft(), equals(5));
    }

    /**
     * @test
     */
    public function pointerIsAtOffsetOfNextLineWhenOneLineOfServeralRead()
    {
        $this->memoryInputStream->readLine();
        assert($this->memoryInputStream->tell(), equals(6));
    }

    /**
     * @test
     */
    public function readLineSplitsOnLineBreakForLastLine()
    {
        $this->memoryInputStream->readLine();
        assert($this->memoryInputStream->readLine(), equals('world'));
    }

    /**
     * @test
     */
    public function hasReachedEofAfterReadingLastLine()
    {
        $this->memoryInputStream->readLine();
        $this->memoryInputStream->readLine();
        assertTrue($this->memoryInputStream->eof());
    }

    /**
     * @test
     */
    public function noyBytesLeftAfterReadingLastLine()
    {
        $this->memoryInputStream->readLine();
        $this->memoryInputStream->readLine();
        assertEquals(0, $this->memoryInputStream->bytesLeft());
    }

    /**
     * @test
     */
    public function pointerIsAtEndAfterReadingLastLine()
    {
        $this->memoryInputStream->readLine();
        $this->memoryInputStream->readLine();
        assertEquals(11, $this->memoryInputStream->tell());
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function readLineWithBothLineBreaks()
    {
        $this->memoryInputStream = new MemoryInputStream("hello\r\nworld");
        assert($this->memoryInputStream->readLine(), equals('hello'));
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function readLineWithBothLineBreaksNextLine()
    {
        $this->memoryInputStream = new MemoryInputStream("hello\r\nworld");
        $this->memoryInputStream->readLine();
        assert($this->memoryInputStream->readLine(), equals('world'));
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
    public function seekCanSetAbsolutePosition()
    {
        $this->memoryInputStream->seek(6);
        assertEquals(6, $this->memoryInputStream->tell());
        assertEquals(5, $this->memoryInputStream->bytesLeft());
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
    }

    /**
     * @test
     */
    public function seekCanSetPositionFromEnd()
    {
        $this->memoryInputStream->seek(-5, Seekable::END);
        assertEquals(6, $this->memoryInputStream->tell());
        assertEquals(5, $this->memoryInputStream->bytesLeft());
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
