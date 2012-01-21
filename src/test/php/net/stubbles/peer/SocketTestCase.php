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
 * Test for net\stubbles\peer\Socket.
 *
 * @group  peer
 */
class SocketTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function createWithEmptyHostThrowsIllegalArgumentException()
    {
        new Socket('');
    }

    /**
     * @test
     */
    public function containsGivenHost()
    {
        $socket = new Socket('example.com');
        $this->assertEquals('example.com', $socket->getHost());
    }

    /**
     * @test
     */
    public function portDefaultsTo80()
    {
        $socket = new Socket('example.com');
        $this->assertEquals(80, $socket->getPort());
    }

    /**
     * @test
     */
    public function hasNoPrefixByDefault()
    {
        $socket = new Socket('example.com');
        $this->assertNull($socket->getPrefix());
    }

    /**
     * @test
     */
    public function timeoutDefaultsTo5Seconds()
    {
        $socket = new Socket('example.com');
        $this->assertEquals(5, $socket->getTimeout());
    }

    /**
     * @test
     */
    public function timeoutCanBeChanged()
    {
        $socket = new Socket('example.com');
        $this->assertEquals(60, $socket->setTimeout(60)->getTimeout());
    }

    /**
     * @test
     */
    public function isNotConnectedAfterCreation()
    {
        $socket = new Socket('example.com');
        $this->assertFalse($socket->isConnected());
    }

    /**
     * @test
     */
    public function isAtEndOfSocketAfterCreation()
    {
        $socket = new Socket('example.com');
        $this->assertTrue($socket->eof());
    }

    /**
     * @test
     */
    public function hasGivenPort()
    {
        $socket = new Socket('example.com', 443, 'ssl://', 30);
        $this->assertEquals(443, $socket->getPort());
    }

    /**
     * @test
     */
    public function hasGivenPrefix()
    {
        $socket = new Socket('example.com', 443, 'ssl://', 30);
        $this->assertEquals('ssl://', $socket->getPrefix());
    }

    /**
     * @test
     */
    public function hasGivenTimeout()
    {
        $socket = new Socket('example.com', 443, 'ssl://', 30);
        $this->assertEquals(30, $socket->getTimeout());
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function readOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new Socket('example.com');
        $socket->read();
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function readLineOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new Socket('example.com');
        $socket->readLine();
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function readBinaryOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new Socket('example.com');
        $socket->readBinary();
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function writeOnUnconnectedThrowsIllegalStateException()
    {
        $socket = new Socket('example.com');
         $socket->write('data');
    }

    /**
     * @test
     */
    public function disconnectReturnsInstance()
    {
        $socket = new Socket('example.com');
        $this->assertSame($socket, $socket->disconnect());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canBeUsedAsInputStream()
    {
        $this->assertInstanceOf('net\\stubbles\\streams\\InputStream',
                                $this->getMock('net\\stubbles\\peer\\Socket',
                                               array('connect'),
                                               array('localhost')
                                       )
                                     ->getInputStream()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canBeUsedAsOutputStream()
    {
        $this->assertInstanceOf('net\\stubbles\\streams\\OutputStream',
                                $this->getMock('net\\stubbles\\peer\\Socket',
                                               array('connect'),
                                               array('localhost')
                                       )
                                     ->getOutputStream()
        );
    }
}
?>