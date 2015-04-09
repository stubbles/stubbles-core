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
 * Test for stubbles\peer\SocketInputStream.
 *
 * @group  peer
 */
class SocketInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\peer\SocketInputStream
     */
    private $socketInputStream;
    /**
     * mocked socket instance
     *
     * @type  \stubbles\peer\Stream
     */
    private $stream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->stream = new Stream(
                fopen(
                        vfsStream::newFile('foo.txt')
                                ->withContent("foo\n")
                                ->at(vfsStream::setup())
                                ->url(),
                        'rb+'
                )
        );
        $this->socketInputStream = new SocketInputStream($this->stream);
    }

    /**
     * @test
     */
    public function readFromSocketWithDefaultLength()
    {
        assertEquals("foo\n", $this->socketInputStream->read());
    }

    /**
     * @test
     */
    public function readFromSocketWithGivenLength()
    {
        assertEquals('foo', $this->socketInputStream->read(3));
    }

    /**
     * @test
     */
    public function readLineFromSocketWithDefaultLength()
    {
        assertEquals('foo', $this->socketInputStream->readLine());
    }

    /**
     * @test
     * @group  foo
     */
    public function readLineFromSocketWithGivenLength()
    {
        assertEquals('foo', $this->socketInputStream->readLine(4));
    }

    /**
     * @test
     */
    public function noBytesLeft()
    {
        $this->socketInputStream->read();
        assertEquals(-1, $this->socketInputStream->bytesLeft());
        assertTrue($this->socketInputStream->eof());
    }

    /**
     * @test
     */
    public function alwaysOneByteLeftWhenNotAtEnd()
    {
        assertEquals(1, $this->socketInputStream->bytesLeft());
        assertFalse($this->socketInputStream->eof());
    }

    /**
     * @test
     * @expectedException  LogicException
     * @since  6.0.0
     */
    public function readOnClosedStreamThrowsLogicException()
    {
        $this->socketInputStream->close();
        $this->socketInputStream->read();
    }

    /**
     * @test
     * @expectedException  LogicException
     * @since  6.0.0
     */
    public function readLineOnClosedStreamThrowsLogicException()
    {
        $this->socketInputStream->close();
        $this->socketInputStream->readLine();
    }

    /**
     * @test
     * @since  6.0.0
     */
    public function eofOnClosedSocketIsAlwaysTrue()
    {
        $this->socketInputStream->close();
        assertTrue($this->socketInputStream->eof());
    }
}
