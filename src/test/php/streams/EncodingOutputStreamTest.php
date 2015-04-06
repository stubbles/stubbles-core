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
use stubbles\streams\memory\MemoryOutputStream;
/**
 * Test for stubbles\streams\EncodingOutputStream.
 *
 * @group  streams
 */
class EncodingOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\EncodingOutputStream
     */
    private $encodingOutputStream;
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
        $this->encodingOutputStream = new EncodingOutputStream(
                $this->memory,
                'iso-8859-1'
        );
    }

    /**
     * @test
     */
    public function knowsGivenCharset()
    {
        assertEquals(
                'iso-8859-1',
                $this->encodingOutputStream->getCharset()
        );
    }

    /**
     * @test
     */
    public function writeEncodesBytesBeforePassedToDecoratedStream()
    {
        assertEquals(5, $this->encodingOutputStream->write('hällö'));
        assertEquals(utf8_decode('hällö'), $this->memory->buffer());
    }

    /**
     * @test
     */
    public function writeLineEncodesBytesBeforePassedToDecoratedStream()
    {
        assertEquals(6, $this->encodingOutputStream->writeLine('hällö'));
        assertEquals(utf8_decode("hällö\n"), $this->memory->buffer());
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesEncodesBytesBeforePassedToDecoratedStream()
    {
        assertEquals(
                12,
                $this->encodingOutputStream->writeLines(['hällö', 'wörld'])
        );
        assertEquals(
                utf8_decode("hällö\nwörld\n"),
                $this->memory->buffer()
        );
    }

    /**
     * @test
     */
    public function closeClosesDecoratedOutputStream()
    {
        $mockOutputStream = $this->getMock('stubbles\streams\OutputStream');
        $mockOutputStream->expects(once())->method('close');
        $encodingOutputStream = new EncodingOutputStream(
                $mockOutputStream,
                'iso-8859-1'
        );
        $encodingOutputStream->close();
    }
}
