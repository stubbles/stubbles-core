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

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
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
        $this->stream = new Stream(fopen(
                vfsStream::newFile('foo.txt')
                        ->withContent("foo\n")
                        ->at(vfsStream::setup())
                        ->url(),
                'rb+'
        ));
        $this->socketInputStream = new SocketInputStream($this->stream);
    }

    /**
     * @test
     */
    public function readFromSocketWithDefaultLength()
    {
        assert($this->socketInputStream->read(), equals("foo\n"));
    }

    /**
     * @test
     */
    public function readFromSocketWithGivenLength()
    {
        assert($this->socketInputStream->read(3), equals('foo'));
    }

    /**
     * @test
     */
    public function readLineFromSocketWithDefaultLength()
    {
        assert($this->socketInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     * @group  foo
     */
    public function readLineFromSocketWithGivenLength()
    {
        assert($this->socketInputStream->readLine(4), equals('foo'));
    }

    /**
     * @test
     */
    public function returnsMinus1WhenNoBytesLeft()
    {
        $this->socketInputStream->read();
        assert($this->socketInputStream->bytesLeft(), equals(-1));
    }

    /**
     * @test
     */
    public function noBytesLeftMeansEof()
    {
        $this->socketInputStream->read();
        assertTrue($this->socketInputStream->eof());
    }

    /**
     * @test
     */
    public function alwaysOneByteLeftWhenNotAtEnd()
    {
        assert($this->socketInputStream->bytesLeft(), equals(1));
    }

    /**
     * @test
     */
    public function positiveAmountOfBytesLeftDoesNotMeanEof()
    {
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
