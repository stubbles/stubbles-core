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
 * Test for net\stubbles\streams\EncodingOutputStream.
 *
 * @group  streams
 */
class EncodingOutputStreamTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  EncodingOutputStream
     */
    protected $encodingOutputStream;
    /**
     * mocked input stream
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockOutputStream     = $this->getMock('net\\stubbles\\streams\\OutputStream');
        $this->encodingOutputStream = new EncodingOutputStream($this->mockOutputStream, 'iso-8859-1');
    }

    /**
     * data send write() should be encoded to charset
     *
     * @test
     */
    public function write()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('write')
                               ->with($this->equalTo(utf8_decode('hällö')))
                               ->will($this->returnValue(5));
        $this->assertEquals(5, $this->encodingOutputStream->write('hällö'));
    }

    /**
     * data send writeLine() should be encoded to charset
     *
     * @test
     */
    public function writeLine()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine')
                               ->with($this->equalTo(utf8_decode('hällö')))
                               ->will($this->returnValue(6));
        $this->assertEquals(6, $this->encodingOutputStream->writeLine('hällö'));
    }

    /**
     * close() should close the inner output stream
     *
     * @test
     */
    public function close()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('close');
        $this->encodingOutputStream->close();
    }
}
?>