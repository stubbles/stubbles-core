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
 * Test for stubbles\peer\BsdSocket.
 *
 * @group  peer
 */
class BsdSocketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * creates partial mock of BsdSocket
     *
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createBsdSocketMock()
    {
        return $this->getMock('stubbles\peer\BsdSocket',
                              ['isConnected', 'disconnect', 'doRead'],
                              [SocketDomain::$AF_INET, 'example.com', 80]
               );
    }

    /**
     * @test
     */
    public function hasGivenDomain()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SocketDomain::$AF_UNIX, $socket->getDomain());
    }

    /**
     * @test
     */
    public function hasGivenHost()
    {
        $socket = createBsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        $this->assertEquals('example.com', $socket->getHost());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function throwsIllegalArgumentExceptionIfPortReqiredButNotGiven()
    {
        $socket = createBsdSocket(SocketDomain::$AF_INET, 'example.com');
        $this->assertNull($socket->getPort());
    }

    /**
     * @test
     */
    public function hasGivenPort()
    {
        $socket = createBsdSocket(SocketDomain::$AF_INET, 'example.com', 21);
        $this->assertEquals(21, $socket->getPort());
    }

    /**
     * @test
     */
    public function portIsNullIfNotRequired()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertNull($socket->getPort());
    }

    /**
     * @test
     */
    public function neverHasPrefix()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertNull($socket->getPrefix());
    }

    /**
     * @test
     */
    public function timeoutDefaultsTo5Seconds()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(5, $socket->getTimeout());
    }

    /**
     * @test
     */
    public function timeoutCanBeChanged()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(60, $socket->setTimeout(60)->getTimeout());
    }

    /**
     * @test
     */
    public function isNotConnectedAfterCreation()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertFalse($socket->isConnected());
    }

    /**
     * @test
     */
    public function isAtEndOfSocketAfterCreation()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertTrue($socket->eof());
    }

    /**
     * @test
     */
    public function typeDefaultsToSOCK_STREAM()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SOCK_STREAM, $socket->getType());
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_DGRAM()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SOCK_DGRAM, $socket->setType(SOCK_DGRAM)->getType());
        $this->assertSame($socket, $socket->setType(SOCK_RAW));
        $this->assertEquals(SOCK_RAW, $socket->getType());
        $this->assertSame($socket, $socket->setType(SOCK_SEQPACKET));
        $this->assertEquals(SOCK_SEQPACKET, $socket->getType());
        $this->assertSame($socket, $socket->setType(SOCK_RDM));
        $this->assertEquals(SOCK_RDM, $socket->getType());
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_RAW()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SOCK_RAW, $socket->setType(SOCK_RAW)->getType());
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_SEQPACKET()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SOCK_SEQPACKET, $socket->setType(SOCK_SEQPACKET)->getType());
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_RDM()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SOCK_RDM, $socket->setType(SOCK_RDM)->getType());
    }

    /**
     * trying to set an invalid type throws an illegal argument exception
     *
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidTypeThrowsIllegalArgumentException()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $socket->setType('invalid');
    }

    /**
     * trying to set the type when connected throws an illegal state exception
     *
     * @test
     * @expectedException  stubbles\lang\exception\IllegalStateException
     */
    public function setTypeWhenConnectedThrowsIllegalStateConnection()
    {
        $socket = $this->createBsdSocketMock();
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->setType(SOCK_SEQPACKET);
    }

    /**
     * @test
     */
    public function protocolDefaultsToTcp()
    {
        $socket = createBsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        $this->assertTrue($socket->isTcp());
        $this->assertFalse($socket->isUdp());
    }

    /**
     * @test
     */
    public function protocolCanBeSetToTcp()
    {
        $socket = createBsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        $this->assertTrue($socket->useTcp()->isTcp());
        $this->assertFalse($socket->isUdp());
    }

    /**
     * @test
     */
    public function protocolCanBeSetToUdp()
    {
        $socket = createBsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        $this->assertTrue($socket->useUdp()->isUdp());
        $this->assertFalse($socket->isTcp());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalStateException
     */
    public function useTcpWhenConnectedThrowsIllegalStateConnection()
    {
        $socket = $this->createBsdSocketMock();
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->useTcp();
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalStateException
     */
    public function useUdpWhenConnectedThrowsIllegalStateConnection()
    {
        $socket = $this->createBsdSocketMock();
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->useUdp();
    }

    /**
     * @test
     */
    public function returnsDefaultForOptionNotSet()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals('default',
                            $socket->getOption('bar', 'baz', 'default')
        );
    }

    /**
     * @test
     */
    public function returnsValueFromOptionAlreadySet()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals('foo',
                            $socket->setOption('bar', 'baz', 'foo')
                                   ->getOption('bar', 'baz', 'default')
        );
    }

    /**
     * @test
     */
    public function readOnConnectedReturnsData()
    {
        $socket = $this->createBsdSocketMock();
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->expects($this->once())
               ->method('doRead')
               ->with($this->equalTo(4096), $this->equalTo(PHP_NORMAL_READ))
               ->will($this->returnValue("foo\n"));
        $this->assertEquals("foo\n", $socket->read());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalStateException
     */
    public function readOnUnconnectedThrowsIllegalStateException()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $socket->read();
    }

    /**
     * @test
     */
    public function readLineOnConnectedReturnsDataWithoutLinebreak()
    {
        $socket = $this->createBsdSocketMock();
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->expects($this->once())
               ->method('doRead')
               ->with($this->equalTo(4096), $this->equalTo(PHP_NORMAL_READ))
               ->will($this->returnValue("foo\n"));
        $this->assertEquals('foo', $socket->readLine());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalStateException
     */
    public function readLineOnUnconnectedThrowsIllegalStateException()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $socket->readLine();
    }

    /**
     * @test
     */
    public function readBinaryOnConnectedReturnsData()
    {
        $socket = $this->createBsdSocketMock();
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->expects($this->once())
               ->method('doRead')
               ->with($this->equalTo(1024), $this->equalTo(PHP_BINARY_READ))
               ->will($this->returnValue("foo\n"));
        $this->assertEquals("foo\n", $socket->readBinary());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalStateException
     */
    public function readBinaryOnUnconnectedThrowsIllegalStateException()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $socket->readBinary();
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalStateException
     */
    public function writeOnUnconnectedThrowsIllegalStateException()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $socket->write('data');
    }

    /**
     * @test
     */
    public function disconnectReturnsInstance()
    {
        $socket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertSame($socket, $socket->disconnect());
    }
}
