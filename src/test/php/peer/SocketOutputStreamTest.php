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
 * Test for stubbles\peer\SocketOutputStream.
 *
 * @group  peer
 */
class SocketOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\peer\SocketOutputStream
     */
    protected $socketOutputStream;
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
        $this->socketOutputStream = new SocketOutputStream($this->mockSocket);
    }

    /**
     * @test
     */
    public function writePassesBytesToSocket()
    {
        $this->mockSocket->method('write')
                ->with(equalTo('foo'))
                ->will(returnValue(3));
        assertEquals(3, $this->socketOutputStream->write('foo'));
    }

    /**
     * @test
     */
    public function writeLinePassesBytesToSocketWithLinebreak()
    {
        $this->mockSocket->method('write')
                ->with(equalTo("foo\r\n"))
                ->will(returnValue(5));
        assertEquals(5, $this->socketOutputStream->writeLine('foo'));
    }

    /**
     * @test
     */
    public function writeLinesPassesBytesToSocketWithLinebreak()
    {
        $this->mockSocket->expects(at(0))
                         ->method('write')
                         ->with(equalTo("foo\r\n"))
                         ->will(returnValue(5));
        $this->mockSocket->expects(at(1))
                         ->method('write')
                         ->with(equalTo("bar\r\n"))
                         ->will(returnValue(5));
        assertEquals(
                10,
                $this->socketOutputStream->writeLines(['foo', 'bar'])
        );
    }

    /**
     * @test
     * @expectedException  LogicException
     * @since  6.0.0
     */
    public function writeOnClosedStreamThrowsLogicException()
    {
        $this->socketOutputStream->close();
        $this->socketOutputStream->write('foo');
    }

    /**
     * @test
     * @expectedException  LogicException
     * @since  6.0.0
     */
    public function writeLinesOnClosedStreamThrowsLogicException()
    {
        $this->socketOutputStream->close();
        $this->socketOutputStream->writeLine('foo');
    }

    /**
     * @test
     * @expectedException  LogicException
     * @since  6.0.0
     */
    public function writeLineOnClosedStreamThrowsLogicException()
    {
        $this->socketOutputStream->close();
        $this->socketOutputStream->writeLines(['foo', 'bar']);
    }
}
