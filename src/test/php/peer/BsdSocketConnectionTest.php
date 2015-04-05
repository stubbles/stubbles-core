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
 * Test for stubbles\peer\BsdSocketConnection.
 *
 * @group  peer
 * @since  6.0.0
 */
class BsdSocketConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * creates partial mock of BsdSocket
     *
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createBsdSocketMock()
    {
        return $this->getMockBuilder(
                        'stubbles\peer\BsdSocketConnection'
                )->disableOriginalConstructor()
                ->setMethods(['doRead'])
                ->getMock();
    }

    /**
     * @test
     */
    public function readReturnsData()
    {
        $bsdSocket = $this->createBsdSocketMock();
        $bsdSocket->method('doRead')
                ->with($this->equalTo(4096), $this->equalTo(PHP_NORMAL_READ))
                ->will($this->returnValue("foo\n"));
        $this->assertEquals("foo\n", $bsdSocket->read());
    }

    /**
     * @test
     */
    public function readLineReturnsDataWithoutLinebreak()
    {
        $bsdSocket = $this->createBsdSocketMock();
        $bsdSocket->method('doRead')
                ->with($this->equalTo(4096), $this->equalTo(PHP_NORMAL_READ))
                ->will($this->returnValue("foo\n"));
        $this->assertEquals('foo', $bsdSocket->readLine());
    }

    /**
     * @test
     */
    public function readBinaryReturnsData()
    {
        $bsdSocket = $this->createBsdSocketMock();
        $bsdSocket->method('doRead')
                ->with($this->equalTo(1024), $this->equalTo(PHP_BINARY_READ))
                ->will($this->returnValue("foo\n"));
        $this->assertEquals("foo\n", $bsdSocket->readBinary());
    }
}
