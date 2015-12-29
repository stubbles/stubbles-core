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

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\memory\MemoryOutputStream.
 *
 * @group  streams
 * @group  streams_memory
 */
class MemoryOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * the file url used in the tests
     *
     * @type  MemoryOutputStream
     */
    protected $memoryOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryOutputStream = new MemoryOutputStream();
    }

    /**
     * @test
     */
    public function bufferIsInitiallyEmpty()
    {
        assert($this->memoryOutputStream->buffer(), equals(''));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function conversionToStringOnEmptyBufferReturnsEmptyString()
    {
        assert((string) $this->memoryOutputStream, equals(''));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function conversionToStringOnWrittenBufferReturnsBufferContents()
    {
        $this->memoryOutputStream->write('hello');
        assert((string) $this->memoryOutputStream, equals('hello'));
    }

    /**
     * @test
     */
    public function writeReturnsAmountOfBytesWritten()
    {
        assert($this->memoryOutputStream->write('hello'), equals(5));
    }

    /**
     * @test
     */
    public function writeWritesBytesIntoBuffer()
    {
        $this->memoryOutputStream->write('hello');
        assert($this->memoryOutputStream->buffer(), equals('hello'));
    }

    /**
     * @test
     */
    public function writeLineReturnsAmountOfBytesWritten()
    {
        assert($this->memoryOutputStream->writeLine('hello'), equals(6));
    }

    /**
     * @test
     */
    public function writeLineWritesBytesIntoBuffer()
    {
        $this->memoryOutputStream->writeLine('hello');
        assert($this->memoryOutputStream->buffer(), equals("hello\n"));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesReturnsAmountOfBytesWritten()
    {
        assert(
                $this->memoryOutputStream->writeLines(['hello', 'world']),
                equals(12)
        );
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesWritesBytesIntoBuffer()
    {
        $this->memoryOutputStream->writeLines(['hello', 'world']);
        assert($this->memoryOutputStream->buffer(), equals("hello\nworld\n"));
    }

    /**
     * @test
     */
    public function closeDoesNothing()
    {
        assertNull($this->memoryOutputStream->close());
    }
}
