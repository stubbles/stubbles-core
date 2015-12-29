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
use bovigo\callmap\NewInstance;
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
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
        assert($this->decodingInputStream->getCharset(), equals('iso-8859-1'));
    }

    /**
     * @test
     */
    public function readReturnsDecodedDataFromDecoratedStream()
    {
        assert($this->decodingInputStream->read(), equals("hällö\n"));
    }

    /**
     * @test
     */
    public function readLineReturnsDecodedLineFromDecoratedStream()
    {
        assert($this->decodingInputStream->readLine(), equals('hällö'));
    }

    /**
     * @test
     */
    public function bytesLeftReturnsBytesLeftFromDecoratedStream()
    {
        assert($this->decodingInputStream->bytesLeft(), equals(6));
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
        $inputStream = NewInstance::of(InputStream::class);
        $decodingInputStream = new DecodingInputStream(
                $inputStream,
                'iso-8859-1'
        );
        $decodingInputStream->close();
        verify($inputStream, 'close')->wasCalledOnce();
    }
}
