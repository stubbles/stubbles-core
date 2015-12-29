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
use stubbles\streams\memory\MemoryOutputStream;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
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
        assert($this->encodingOutputStream->getCharset(), equals('iso-8859-1'));
    }

    /**
     * @test
     */
    public function writeEncodesBytesBeforePassedToDecoratedStream()
    {
        assert($this->encodingOutputStream->write('hällö'), equals(5));
        assert($this->memory->buffer(), equals(utf8_decode('hällö')));
    }

    /**
     * @test
     */
    public function writeLineEncodesBytesBeforePassedToDecoratedStream()
    {
        assert($this->encodingOutputStream->writeLine('hällö'), equals(6));
        assert($this->memory->buffer(), equals(utf8_decode("hällö\n")));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesEncodesBytesBeforePassedToDecoratedStream()
    {
        assert(
                $this->encodingOutputStream->writeLines(['hällö', 'wörld']),
                equals(12)
        );
        assert($this->memory->buffer(), equals(utf8_decode("hällö\nwörld\n")));
    }

    /**
     * @test
     */
    public function closeClosesDecoratedOutputStream()
    {
        $outputStream = NewInstance::of(OutputStream::class);
        $encodingOutputStream = new EncodingOutputStream(
                $outputStream,
                'iso-8859-1'
        );
        $encodingOutputStream->close();
        verify($outputStream, 'close')->wasCalledOnce();
    }
}
