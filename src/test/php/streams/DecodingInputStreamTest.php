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
 * Test for stubbles\streams\DecodingInputStream.
 *
 * @group  streams
 */
class DecodingInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\DecodingInputStream
     */
    private $decodingInputStream;
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
        $this->memory = new MemoryInputStream(utf8_decode("hällö\n"));
        $this->decodingInputStream = new DecodingInputStream(
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
                $this->decodingInputStream->getCharset()
        );
    }

    /**
     * @test
     */
    public function readReturnsDecodedDataFromDecoratedStream()
    {
        assertEquals("hällö\n", $this->decodingInputStream->read());
    }

    /**
     * @test
     */
    public function readLineReturnsDecodedLineFromDecoratedStream()
    {
        assertEquals('hällö', $this->decodingInputStream->readLine());
    }

    /**
     * @test
     */
    public function bytesLeftReturnsBytesLeftFromDecoratedStream()
    {
        assertEquals(6, $this->decodingInputStream->bytesLeft());
    }

    /**
     * @test
     */
    public function eofReturnsEofFromDecoratedStream()
    {
        assertFalse($this->decodingInputStream->eof());
    }

    /**
     * @test
     */
    public function closeClosesDecoratedStream()
    {
        $mockInputStream = $this->getMock('stubbles\streams\InputStream');
        $mockInputStream->expects(once())->method('close');
        $decodingInputStream = new DecodingInputStream(
                $mockInputStream,
                'iso-8859-1'
        );
        $decodingInputStream->close();
    }
}
