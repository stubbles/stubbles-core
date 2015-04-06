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
        assertEquals('', $this->memoryOutputStream->buffer());
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function conversionToStringOnEmptyBufferReturnsEmptyString()
    {
        assertEquals('', (string) $this->memoryOutputStream);
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function conversionToStringOnWrittenBufferReturnsBufferContents()
    {
        $this->memoryOutputStream->write('hello');
        assertEquals('hello', (string) $this->memoryOutputStream);
    }

    /**
     * @test
     */
    public function writeWritesBytesIntoBuffer()
    {
        assertEquals(5, $this->memoryOutputStream->write('hello'));
        assertEquals('hello', $this->memoryOutputStream->buffer());
    }

    /**
     * @test
     */
    public function writeLineWritesBytesIntoBuffer()
    {
        assertEquals(6, $this->memoryOutputStream->writeLine('hello'));
        assertEquals("hello\n", $this->memoryOutputStream->buffer());
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesWritesBytesIntoBuffer()
    {
        assertEquals(12, $this->memoryOutputStream->writeLines(['hello', 'world']));
        assertEquals("hello\nworld\n", $this->memoryOutputStream->buffer());
    }

    /**
     * @test
     */
    public function closeDoesNothing()
    {
        assertNull($this->memoryOutputStream->close());
    }
}
