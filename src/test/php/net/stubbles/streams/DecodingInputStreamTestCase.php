<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams;
/**
 * Test for net\stubbles\streams\DecodingInputStream.
 *
 * @group  streams
 */
class DecodingInputStreamTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  DecodingInputStream
     */
    protected $decodingInputStream;
    /**
     * mocked input stream
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInputStream     = $this->getMock('net\\stubbles\\streams\\InputStream');
        $this->decodingInputStream = new DecodingInputStream($this->mockInputStream, 'iso-8859-1');
    }

    /**
     * @test
     */
    public function knowsGivenCharset()
    {
        $this->assertEquals('iso-8859-1', $this->decodingInputStream->getCharset());
    }

    /**
     * data returned from read() should be decoded to UTF-8
     *
     * @test
     */
    public function read()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('read')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue(utf8_decode('hällö')));
        $this->assertEquals('hällö', $this->decodingInputStream->read());
    }

    /**
     * data returned from readLine() should be decoded to UTF-8
     *
     * @test
     */
    public function readLine()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('readLine')
                              ->with($this->equalTo(8192))
                              ->will($this->returnValue(utf8_decode('hällö')));
        $this->assertEquals('hällö', $this->decodingInputStream->readLine());
    }

    /**
     * data returned from bytesLeft() should be returned
     *
     * @test
     */
    public function bytesLeft()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('bytesLeft')
                              ->will($this->returnValue(5));
        $this->assertEquals(5, $this->decodingInputStream->bytesLeft());
    }

    /**
     * data returned from eof() should be returned
     *
     * @test
     */
    public function eof()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('eof')
                              ->will($this->returnValue(false));
        $this->assertFalse($this->decodingInputStream->eof());
    }

    /**
     * close() should close the inner input stream
     *
     * @test
     */
    public function close()
    {
        $this->mockInputStream->expects($this->once())
                              ->method('close');
        $this->decodingInputStream->close();
    }
}
?>