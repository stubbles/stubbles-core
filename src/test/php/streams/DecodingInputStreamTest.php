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
 * Test for stubbles\streams\DecodingInputStream.
 *
 * @group  streams
 */
class DecodingInputStreamTest extends \PHPUnit_Framework_TestCase
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
        $this->mockInputStream     = $this->getMock('stubbles\streams\InputStream');
        $this->decodingInputStream = new DecodingInputStream(
                $this->mockInputStream,
                'iso-8859-1'
        );
    }

    /**
     * @test
     */
    public function knowsGivenCharset()
    {
        $this->assertEquals('iso-8859-1', $this->decodingInputStream->getCharset());
    }

    /**
     * @test
     */
    public function readReturnsDecodedDataFromDecoratedStream()
    {
        $this->mockInputStream->method('read')
                ->with(equalTo(8192))
                ->will(returnValue(utf8_decode('hällö')));
        $this->assertEquals('hällö', $this->decodingInputStream->read());
    }

    /**
     * @test
     */
    public function readLineReturnsDecodedLineFromDecoratedStream()
    {
        $this->mockInputStream->method('readLine')
                ->with(equalTo(8192))
                ->will(returnValue(utf8_decode('hällö')));
        $this->assertEquals('hällö', $this->decodingInputStream->readLine());
    }

    /**
     * @test
     */
    public function bytesLeftReturnsBytesLeftFromDecoratedStream()
    {
        $this->mockInputStream->method('bytesLeft')->will(returnValue(5));
        $this->assertEquals(5, $this->decodingInputStream->bytesLeft());
    }

    /**
     * @test
     */
    public function eofReturnsEofFromDecoratedStream()
    {
        $this->mockInputStream->method('eof')->will(returnValue(false));
        $this->assertFalse($this->decodingInputStream->eof());
    }

    /**
     * @test
     */
    public function closeClosesDecoratedStream()
    {
        $this->mockInputStream->expects(once())->method('close');
        $this->decodingInputStream->close();
    }
}
