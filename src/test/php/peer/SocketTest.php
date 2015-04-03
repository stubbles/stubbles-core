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
 * Test for stubbles\peer\Socket.
 *
 * @group  peer
 */
class SocketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function createWithEmptyHostThrowsIllegalArgumentException()
    {
        createSocket('');
    }

    /**
     * @test
     */
    public function isNotSecureByDefault()
    {
        $socket = createSocket('example.com');
        $this->assertFalse($socket->usesSsl());
    }

    /**
     * @test
     */
    public function timeoutDefaultsTo5Seconds()
    {
        $socket = createSocket('example.com');
        $this->assertEquals(5, $socket->timeout());
    }

    /**
     * @test
     */
    public function timeoutCanBeChanged()
    {
        $socket = createSocket('example.com');
        $this->assertEquals(60, $socket->setTimeout(60)->timeout());
    }

    /**
     * @test
     */
    public function isNotConnectedAfterCreation()
    {
        $socket = createSocket('example.com');
        $this->assertFalse($socket->isConnected());
    }

    /**
     * @test
     */
    public function isAtEndOfSocketAfterCreation()
    {
        $socket = createSocket('example.com');
        $this->assertTrue($socket->eof());
    }

    /**
     * @test
     */
    public function isSecureWhenPrefixForSslGiven()
    {
        $socket = createSocket('example.com', 443, 'ssl://', 30);
        $this->assertTrue($socket->usesSsl());
    }

    /**
     * @test
     */
    public function hasGivenTimeout()
    {
        $socket = createSocket('example.com', 443, 'ssl://', 30);
        $this->assertEquals(30, $socket->timeout());
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function readOnUnconnectedThrowsIllegalStateException()
    {
        $socket = createSocket('example.com');
        $socket->read();
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function readLineOnUnconnectedThrowsIllegalStateException()
    {
        $socket = createSocket('example.com');
        $socket->readLine();
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function readBinaryOnUnconnectedThrowsIllegalStateException()
    {
        $socket = createSocket('example.com');
        $socket->readBinary();
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function writeOnUnconnectedThrowsIllegalStateException()
    {
        $socket = createSocket('example.com');
         $socket->write('data');
    }

    /**
     * @test
     */
    public function disconnectReturnsInstance()
    {
        $socket = createSocket('example.com');
        $this->assertSame($socket, $socket->disconnect());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canBeUsedAsInputStream()
    {
        $this->assertInstanceOf('stubbles\streams\InputStream',
                                $this->getMock('stubbles\peer\Socket',
                                               ['connect'],
                                               ['localhost']
                                       )
                                     ->in()
        );
    }

    /**
     * @since  4.0.0
     * @test
     */
    public function alwaysReturnsSameInputStream()
    {
        $socket = $this->getMock('stubbles\peer\Socket', ['connect'], ['localhost']);
        $this->assertSame($socket->in(), $socket->in());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canBeUsedAsOutputStream()
    {
        $this->assertInstanceOf('stubbles\streams\OutputStream',
                                $this->getMock('stubbles\peer\Socket',
                                               ['connect'],
                                               ['localhost']
                                       )
                                     ->out()
        );
    }

    /**
     * @since  4.0.0
     * @test
     */
    public function alwaysReturnsSameOutputStream()
    {
        $socket = $this->getMock('stubbles\peer\Socket', ['connect'], ['localhost']);
        $this->assertSame($socket->out(), $socket->out());
    }
}
