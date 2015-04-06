<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer;
use org\bovigo\vfs\vfsStream;
/**
 * Test for stubbles\peer\Stream.
 *
 * @group  peer
 * @since  6.0.0
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type  org\bovigo\vfs\vfsStreamFile
     */
    private $file;
    /**
     * @type  resource
     */
    private $underlyingStream;
    /**
     * @type  \stubbles\peer\Stream
     */
    private $stream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $root = vfsStream::setup();
        $this->file = vfsStream::newFile('foo.txt')
                ->withContent("bar\nbaz")
                ->at($root);
        $this->underlyingStream = fopen($this->file->url(), 'rb+');
        $this->stream = new Stream($this->underlyingStream);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function createWithInvalidResourceThrowsIllegalArgumentException()
    {
        new Stream('foo');
    }

    /**
     * @test
     */
    public function readReturnsDataOfFirstLine()
    {
        assertEquals("bar\n", $this->stream->read());
    }

    /**
     * @test
     */
    public function readLineReturnsTrimmedDataOfFirstLine()
    {
        assertEquals('bar', $this->stream->readLine());
    }

    /**
     * @test
     */
    public function readBinaryReturnsData()
    {
        assertEquals("bar\nbaz", $this->stream->readBinary());
    }

    /**
     * @test
     */
    public function writesToResource()
    {
        assertEquals(8, $this->stream->write('yoyoyoyo'));
        assertEquals('yoyoyoyo', $this->file->getContent());
    }

    /**
     * @test
     */
    public function eofReturnsTrueWhenNotAtEnd()
    {
        assertFalse($this->stream->eof());
    }

    /**
     * @test
     */
    public function eofReturnsTrueWhenAtEnd()
    {
        $this->stream->readBinary();
        assertTrue($this->stream->eof());
    }

    /**
     * @test
     */
    public function canBeUsedAsInputStream()
    {
        assertInstanceOf(
                'stubbles\streams\InputStream',
                $this->stream->in()
        );
    }

    /**
     * @test
     */
    public function alwaysReturnsSameInputStream()
    {
        assertSame(
                $this->stream->in(),
                $this->stream->in()
        );
    }

    /**
     * @test
     */
    public function canBeUsedAsOutputStream()
    {
        assertInstanceOf(
                'stubbles\streams\OutputStream',
                $this->stream->out()
        );
    }

    /**
     * @test
     */
    public function alwaysReturnsSameOutputStream()
    {
        assertSame(
                $this->stream->out(),
                $this->stream->out()
        );
    }

    /**
     * @test
     */
    public function nullingTheStreamClosesTheResource()
    {
        $this->stream = null;
        assertFalse(is_resource($this->underlyingStream));
    }
}
