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
    protected $socketInputStream;
    /**
     * mocked socket instance
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSocket;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockSocket = $this->getMockBuilder('stubbles\peer\Stream')
                ->disableOriginalConstructor()
                ->getMock();
        $this->socketInputStream = new SocketInputStream($this->mockSocket);
    }

    /**
     * @test
     */
    public function readFromSocketWithDefaultLength()
    {
        $this->mockSocket->expects($this->once())
                         ->method('readBinary')
                         ->with($this->equalTo(8192))
                         ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->socketInputStream->read());
    }

    /**
     * @test
     */
    public function readFromSocketWithGivenLength()
    {
        $this->mockSocket->expects($this->once())
                         ->method('readBinary')
                         ->with($this->equalTo(3))
                         ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->socketInputStream->read(3));
    }

    /**
     * @test
     */
    public function readLineFromSocketWithDefaultLength()
    {
        $this->mockSocket->expects($this->once())
                         ->method('readLine')
                         ->with($this->equalTo(8192))
                         ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->socketInputStream->readLine());
    }

    /**
     * @test
     */
    public function readLineFromSocketWithGivenLength()
    {
        $this->mockSocket->expects($this->once())
                         ->method('readLine')
                         ->with($this->equalTo(3))
                         ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->socketInputStream->readLine(3));
    }

    /**
     * @test
     */
    public function noBytesLeft()
    {
        $this->mockSocket->expects($this->exactly(2))
                         ->method('eof')
                         ->will($this->returnValue(true));
        $this->assertEquals(-1, $this->socketInputStream->bytesLeft());
        $this->assertTrue($this->socketInputStream->eof());
    }

    /**
     * @test
     */
    public function bytesLeft()
    {
        $this->mockSocket->expects($this->exactly(2))
                         ->method('eof')
                         ->will($this->returnValue(false));
        $this->assertEquals(1, $this->socketInputStream->bytesLeft());
        $this->assertFalse($this->socketInputStream->eof());
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
        $this->assertTrue($this->socketInputStream->eof());
    }
}
