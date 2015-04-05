<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams;
/**
 * Test for stubbles\streams\EncodingOutputStream.
 *
 * @group  streams
 */
class EncodingOutputStreamTest extends \PHPUnit_Framework_TestCase
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
        $this->mockOutputStream     = $this->getMock('stubbles\streams\OutputStream');
        $this->encodingOutputStream = new EncodingOutputStream($this->mockOutputStream, 'iso-8859-1');
    }

    /**
     * @test
     */
    public function knowsGivenCharset()
    {
        $this->assertEquals('iso-8859-1', $this->encodingOutputStream->getCharset());
    }

    /**
     * @test
     */
    public function writeEncodesBytesBeforePassedToDecoratedStream()
    {
        $this->mockOutputStream->expects($this->once())
                ->method('write')
                ->with($this->equalTo(utf8_decode('hällö')))
                ->will($this->returnValue(5));
        $this->assertEquals(5, $this->encodingOutputStream->write('hällö'));
    }

    /**
     * @test
     */
    public function writeLineEncodesBytesBeforePassedToDecoratedStream()
    {
        $this->mockOutputStream->expects($this->once())
                ->method('writeLine')
                ->with($this->equalTo(utf8_decode('hällö')))
                ->will($this->returnValue(6));
        $this->assertEquals(6, $this->encodingOutputStream->writeLine('hällö'));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesEncodesBytesBeforePassedToDecoratedStream()
    {
        $this->mockOutputStream->expects($this->at(0))
                ->method('writeLine')
                ->with($this->equalTo(utf8_decode('hällö')))
                ->will($this->returnValue(6));
        $this->mockOutputStream->expects($this->at(1))
                ->method('writeLine')
                ->with($this->equalTo(utf8_decode('wörld')))
                ->will($this->returnValue(6));
        $this->assertEquals(12, $this->encodingOutputStream->writeLines(['hällö', 'wörld']));
    }

    /**
     * @test
     */
    public function closeClosesDecoratedOutputStream()
    {
        $this->mockOutputStream->expects($this->once())->method('close');
        $this->encodingOutputStream->close();
    }
}
