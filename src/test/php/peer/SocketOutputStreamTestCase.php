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
class SocketOutputStreamTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  SocketOutputStream
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
        $this->mockSocket        = $this->getMock('stubbles\peer\Socket', array(), array('example.com'));
        $this->mockSocket->expects($this->once())
                         ->method('connect');
        $this->socketOutputStream = new SocketOutputStream($this->mockSocket);
    }

    /**
     * @test
     */
    public function writePassesBytesToSocket()
    {
        $this->mockSocket->expects($this->once())
                         ->method('write')
                         ->with($this->equalTo('foo'))
                         ->will($this->returnValue(3));
        $this->assertEquals(3, $this->socketOutputStream->write('foo'));
    }

    /**
     * @test
     */
    public function writeLinePassesBytesToSocketWithLinebreak()
    {
        $this->mockSocket->expects($this->once())
                         ->method('write')
                         ->with($this->equalTo("foo\r\n"))
                         ->will($this->returnValue(5));
        $this->assertEquals(5, $this->socketOutputStream->writeLine('foo'));
    }

    /**
     * @test
     */
    public function writeLinesPassesBytesToSocketWithLinebreak()
    {
        $this->mockSocket->expects($this->at(0))
                         ->method('write')
                         ->with($this->equalTo("foo\r\n"))
                         ->will($this->returnValue(5));
        $this->mockSocket->expects($this->at(1))
                         ->method('write')
                         ->with($this->equalTo("bar\r\n"))
                         ->will($this->returnValue(5));
        $this->assertEquals(10, $this->socketOutputStream->writeLines(array('foo', 'bar')));
    }

    /**
     * @test
     */
    public function closingTheStreamDisconnectsTheSocket()
    {
        $this->mockSocket->expects($this->atLeastOnce())
                         ->method('disconnect');
        $this->socketOutputStream->close();
    }
}
