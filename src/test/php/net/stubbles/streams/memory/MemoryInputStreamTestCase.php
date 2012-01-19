<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams\memory;
use net\stubbles\streams\Seekable;
/**
 * Test for net\stubbles\streams\memory\MemoryInputStream.
 *
 * @group  streams
 * @group  streams_memory
 */
class MemoryInputStreamTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * the file url used in the tests
     *
     * @type  MemoryInputStream
     */
    protected $memoryInputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryInputStream = new MemoryInputStream("hello\nworld");
    }

    /**
     * write() puts data into buffer
     *
     * @test
     */
    public function read()
    {
        $this->assertFalse($this->memoryInputStream->eof());
        $this->assertEquals(11, $this->memoryInputStream->bytesLeft());
        $this->assertEquals(0, $this->memoryInputStream->tell());
        $this->assertEquals("hello\nworld", $this->memoryInputStream->read());
        $this->assertTrue($this->memoryInputStream->eof());
        $this->assertEquals(0, $this->memoryInputStream->bytesLeft());
        $this->assertEquals(11, $this->memoryInputStream->tell());
    }

    /**
     * writeLine() puts data into buffer
     *
     * @test
     */
    public function readLine()
    {
        $this->assertFalse($this->memoryInputStream->eof());
        $this->assertEquals(11, $this->memoryInputStream->bytesLeft());
        $this->assertEquals(0, $this->memoryInputStream->tell());
        $this->assertEquals('hello', $this->memoryInputStream->readLine());
        $this->assertFalse($this->memoryInputStream->eof());
        $this->assertEquals(5, $this->memoryInputStream->bytesLeft());
        $this->assertEquals(6, $this->memoryInputStream->tell());
        $this->assertEquals('world', $this->memoryInputStream->readLine());
        $this->assertTrue($this->memoryInputStream->eof());
        $this->assertEquals(0, $this->memoryInputStream->bytesLeft());
        $this->assertEquals(11, $this->memoryInputStream->tell());
    }

    /**
     * close() does nothing
     *
     * @test
     */
    public function close()
    {
        $this->memoryInputStream->close();
    }

    /**
     * seek() sets position of of buffer
     *
     * @test
     */
    public function seek_SET()
    {
        $this->memoryInputStream->seek(6);
        $this->assertEquals(6, $this->memoryInputStream->tell());
        $this->assertEquals(5, $this->memoryInputStream->bytesLeft());
        $this->assertFalse($this->memoryInputStream->eof());
        $this->assertEquals('world', $this->memoryInputStream->read());
        $this->memoryInputStream->seek(0, Seekable::SET);
        $this->assertEquals(11, $this->memoryInputStream->bytesLeft());
        $this->assertEquals(0, $this->memoryInputStream->tell());
        $this->assertEquals("hello\nworld", $this->memoryInputStream->read());
    }

    /**
     * seek() sets position of of buffer
     *
     * @test
     */
    public function seek_CURRENT()
    {
        $this->memoryInputStream->read(4);
        $this->memoryInputStream->seek(2, Seekable::CURRENT);
        $this->assertEquals(6, $this->memoryInputStream->tell());
        $this->assertEquals(5, $this->memoryInputStream->bytesLeft());
        $this->assertFalse($this->memoryInputStream->eof());
        $this->assertEquals('world', $this->memoryInputStream->read());
    }

    /**
     * seek() sets position of of buffer
     *
     * @test
     */
    public function seek_END()
    {
        $this->memoryInputStream->seek(-5, Seekable::END);
        $this->assertEquals(6, $this->memoryInputStream->tell());
        $this->assertEquals(5, $this->memoryInputStream->bytesLeft());
        $this->assertFalse($this->memoryInputStream->eof());
        $this->assertEquals('world', $this->memoryInputStream->read());
    }

    /**
     * seek() sets position of of buffer
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function seek_invalidWhence()
    {
        $this->memoryInputStream->seek(6, 66);
    }
}
?>