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
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function throwsIllegalArgumentExceptionIfPortReqiredButNotGiven()
    {
        createBsdSocket(SocketDomain::$AF_INET, 'example.com');
    }

    /**
     * @test
     */
    public function typeDefaultsToSOCK_STREAM()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        assertEquals(SOCK_STREAM, $bsdSocket->type());
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_DGRAM()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        assertEquals(SOCK_DGRAM, $bsdSocket->setType(SOCK_DGRAM)->type());
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_RAW()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        assertEquals(SOCK_RAW, $bsdSocket->setType(SOCK_RAW)->type());
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_SEQPACKET()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        assertEquals(
                SOCK_SEQPACKET,
                $bsdSocket->setType(SOCK_SEQPACKET)->type()
        );
    }

    /**
     * @test
     */
    public function typeCanBeSetToSOCK_RDM()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        assertEquals(SOCK_RDM, $bsdSocket->setType(SOCK_RDM)->type());
    }

    /**
     * trying to set an invalid type throws an illegal argument exception
     *
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function invalidTypeThrowsIllegalArgumentException()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        $bsdSocket->setType('invalid');
    }

    /**
     * @test
     */
    public function protocolDefaultsToTcp()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        assertTrue($bsdSocket->isTcp());
        assertFalse($bsdSocket->isUdp());
    }

    /**
     * @test
     */
    public function protocolCanBeSetToTcp()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        assertTrue($bsdSocket->useTcp()->isTcp());
        assertFalse($bsdSocket->isUdp());
    }

    /**
     * @test
     */
    public function protocolCanBeSetToUdp()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_INET, 'example.com', 80);
        assertTrue($bsdSocket->useUdp()->isUdp());
        assertFalse($bsdSocket->isTcp());
    }

    /**
     * @test
     */
    public function returnsDefaultForOptionNotSet()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        assertEquals(
                'default',
                $bsdSocket->option('bar', 'baz', 'default')
        );
    }

    /**
     * @test
     */
    public function returnsValueFromOptionAlreadySet()
    {
        $bsdSocket = createBsdSocket(SocketDomain::$AF_UNIX, '/tmp/mysocket');
        assertEquals(
                'foo',
                $bsdSocket->setOption('bar', 'baz', 'foo')
                        ->option('bar', 'baz', 'default')
        );
    }
}
