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
use org\bovigo\vfs\vfsStream;
/**
 * Helper class for the test.
 */
class TestResourceInputStream extends ResourceInputStream
{
    /**
     * constructor
     *
     * @param   resource  $handle
     */
    public function __construct($handle)
    {
        $this->setHandle($handle);
    }
}
/**
 * Test for stubbles\streams\ResourceInputStream.
 *
 * @group  streams
 */
class ResourceInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  TestResourceInputStream
     */
    protected $resourceInputStream;
    /**
     * the handle
     *
     * @type  resource
     */
    protected $handle;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $root = vfsStream::setup();
        vfsStream::newFile('test_read.txt')
                 ->withContent('foobarbaz
jjj')
                 ->at($root);
        $this->handle              = fopen(vfsStream::url('root/test_read.txt'), 'r');
        $this->resourceInputStream = new TestResourceInputStream($this->handle);
    }

    /**
     * try to create an instance with an invalid handle
     *
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function invalidHandleThrowsIllegalArgumentException()
    {
        new TestResourceInputStream('invalid');
    }

    /**
     * read data from resource
     *
     * @test
     */
    public function read()
    {
        assertEquals(13, $this->resourceInputStream->bytesLeft());
        assertEquals("foobarbaz\njjj", $this->resourceInputStream->read());
        assertEquals(0, $this->resourceInputStream->bytesLeft());
    }

    /**
     * read data from resource
     *
     * @test
     */
    public function readBytes()
    {
        assertEquals(13, $this->resourceInputStream->bytesLeft());
        assertEquals('foobar', $this->resourceInputStream->read(6));
        assertEquals(7, $this->resourceInputStream->bytesLeft());
    }

    /**
     * read data from resource
     *
     * @test
     */
    public function readLine()
    {
        assertEquals(13, $this->resourceInputStream->bytesLeft());
        assertEquals('foobarbaz', $this->resourceInputStream->readLine());
        assertEquals(3, $this->resourceInputStream->bytesLeft());
    }

    /**
     * check end of file pointer
     *
     * @test
     */
    public function endOfFile()
    {
        assertFalse($this->resourceInputStream->eof());
        $this->resourceInputStream->read();
        assertTrue($this->resourceInputStream->eof());
        assertEquals('', $this->resourceInputStream->read());
        assertEquals('', $this->resourceInputStream->read());
    }

    /**
     * check end of file pointer
     *
     * @test
     */
    public function endOfFileReadLine()
    {
        assertFalse($this->resourceInputStream->eof());
        assertEquals('foobarbaz', $this->resourceInputStream->readLine());
        assertFalse($this->resourceInputStream->eof());
        assertEquals('jjj', $this->resourceInputStream->readLine());
        assertTrue($this->resourceInputStream->eof());
        assertEquals('', $this->resourceInputStream->readLine());
    }

    /**
     * trying to read fails after resource was closed
     *
     * @test
     * @expectedException  LogicException
     */
    public function readAfterCloseFails()
    {
        $this->resourceInputStream->close();
        $this->resourceInputStream->read();
    }

    /**
     * trying to read fails after resource was closed
     *
     * @test
     * @expectedException  LogicException
     */
    public function readLineAfterCloseFails()
    {
        $this->resourceInputStream->close();
        $this->resourceInputStream->readLine();
    }

    /**
     * trying to ask for left bytes to read fails after resource was closed
     *
     * @test
     * @expectedException  LogicException
     */
    public function bytesLeftAfterCloseFails()
    {
        $this->resourceInputStream->close();
        $this->resourceInputStream->bytesLeft();
    }

    /**
     * trying to read fails after resource was closed
     *
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function readAfterCloseFromOutsite()
    {
        fclose($this->handle);
        $this->resourceInputStream->read();
    }

    /**
     * trying to read fails after resource was closed
     *
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function readLineAfterCloseFromOutsite()
    {
        fclose($this->handle);
        $this->resourceInputStream->readLine();
    }

    /**
     * trying to ask for left bytes to read fails after resource was closed
     *
     * @test
     * @expectedException  LogicException
     */
    public function bytesLeftAfterCloseFromOutsite()
    {
        fclose($this->handle);
        $this->resourceInputStream->bytesLeft();
    }
}
