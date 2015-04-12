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
use bovigo\callmap;
use bovigo\callmap\NewInstance;
use stubbles\streams\memory\MemoryOutputStream;
/**
 * Helper class for the test to make abstract class instantiable.
 */
class TestAbstractDecoratedOutputStream extends AbstractDecoratedOutputStream
{
    // intentionally empty
}
/**
 * Test for stubbles\streams\AbstractDecoratedOutputStream.
 *
 * @group streams
 */
class AbstractDecoratedOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\AbstractDecoratedOutputStream
     */
    private $abstractDecoratedOutputStream;
    /**
     * mocked input stream
     *
     * @type  \stubbles\streams\memory\MemoryOutputStream
     */
    private $memory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memory = new MemoryOutputStream();
        $this->abstractDecoratedOutputStream = new TestAbstractDecoratedOutputStream($this->memory);
    }

    /**
     * @test
     */
    public function writeCallsDecoratedStream()
    {
        assertEquals(
                3,
                $this->abstractDecoratedOutputStream->write('foo')
        );
        assertEquals('foo', $this->memory->buffer());
    }

    /**
     * @test
     */
    public function writeLineCallsDecoratedStream()
    {
        assertEquals(
                4,
                $this->abstractDecoratedOutputStream->writeLine('foo')
        );
        assertEquals("foo\n", $this->memory->buffer());
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesCallsDecoratedStream()
    {
        assertEquals(
                8,
                $this->abstractDecoratedOutputStream->writeLines(['foo', 'bar'])
        );
        assertEquals("foo\nbar\n", $this->memory->buffer());
    }

    /**
     * @test
     */
    public function closeClosesDecoratedStream()
    {
        $outputStream = NewInstance::of('stubbles\streams\OutputStream');
        $abstractDecoratedOutputStream = new TestAbstractDecoratedOutputStream(
                $outputStream
        );
        $abstractDecoratedOutputStream->close();
        callmap\verify($outputStream, 'close')->wasCalledOnce();
    }
}
