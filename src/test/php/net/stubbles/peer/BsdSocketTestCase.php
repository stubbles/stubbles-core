<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\peer;
/**
 * Test for net\stubbles\peer\BsdSocket.
 *
 * @group  peer
 */
class BsdSocketTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * creates partial mock of BsdSocket
     *
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createBsdSocketMock()
    {
        return $this->getMock('net\\stubbles\\peer\\BsdSocket',
                              array('isConnected', 'disconnect', 'doRead'),
                              array(SocketDomain::$AF_INET, 'example.com', 80)
               );
    }

    /**
     * @test
     */
    public function hasGivenDomain()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SocketDomain::$AF_UNIX, $socket->getDomain());
    }

    /**
     * @test
     */
    public function hasGivenHost()
    {
        $socket = new BsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        $this->assertEquals('example.com', $socket->getHost());
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function throwsIllegalArgumentExceptionIfPortReqiredButNotGiven()
    {
        $socket = new BsdSocket(SocketDomain::$AF_INET, 'example.com');
        $this->assertNull($socket->getPort());
    }

    /**
     * @test
     */
    public function hasGivenPort()
    {
        $socket = new BsdSocket(SocketDomain::$AF_INET, 'example.com', 21);
        $this->assertEquals(21, $socket->getPort());
    }

    /**
     * @test
     */
    public function portIsNullIfNotRequired()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertNull($socket->getPort());
    }

    /**
     * @test
     */
    public function neverHasPrefix()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertNull($socket->getPrefix());
    }

    /**
     * @test
     */
    public function timeoutDefaultsTo5Seconds()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(5, $socket->getTimeout());
    }

    /**
     * @test
     */
    public function timeoutCanBeChanged()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(60, $socket->setTimeout(60)->getTimeout());
    }

    /**
     * @test
     */
    public function isNotConnectedAfterCreation()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertFalse($socket->isConnected());
    }

    /**
     * @test
     */
    public function isAtEndOfSocketAfterCreation()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertTrue($socket->eof());
    }

    /**
     * @test
     */
    public function typeDefaultsToSOCK_STREAM()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SOCK_STREAM, $socket->getType());
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_DGRAM()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
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
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SOCK_RAW, $socket->setType(SOCK_RAW)->getType());
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_SEQPACKET()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SOCK_SEQPACKET, $socket->setType(SOCK_SEQPACKET)->getType());
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_RDM()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals(SOCK_RDM, $socket->setType(SOCK_RDM)->getType());
    }

    /**
     * trying to set an invalid type throws an illegal argument exception
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidTypeThrowsIllegalArgumentException()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $socket->setType('invalid');
    }

    /**
     * trying to set the type when connected throws an illegal state exception
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
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
        $socket = new BsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        $this->assertTrue($socket->isTcp());
        $this->assertFalse($socket->isUdp());
    }

    /**
     * @test
     */
    public function protocolCanBeSetToTcp()
    {
        $socket = new BsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        $this->assertTrue($socket->useTcp()->isTcp());
        $this->assertFalse($socket->isUdp());
    }

    /**
     * @test
     */
    public function protocolCanBeSetToUdp()
    {
        $socket = new BsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        $this->assertTrue($socket->useUdp()->isUdp());
        $this->assertFalse($socket->isTcp());
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function useTcpWhenConnectedThrowsIllegalStateConnection()
    {
        $socket = $this->createBsdSocketMock();
        $socket->expects($this->any())->method('isConnected')->will($this->returnValue(true));
        $socket->useTcp();
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
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
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertEquals('default',
                            $socket->getOption('bar', 'baz', 'default')
        );
    }

    /**
     * @test
     */
    public function returnsValueFromOptionAlreadySet()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
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
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function readOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
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
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function readLineOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
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
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function readBinaryOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $socket->readBinary();
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function writeOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $socket->write('data');
    }

    /**
     * @test
     */
    public function disconnectReturnsInstance()
    {
        $socket = new BsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $this->assertSame($socket, $socket->disconnect());
    }
}
?>