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
 * Test for net\stubbles\streams\ResourceInputStream.
 *
 * @group  streams
 */
class ResourceInputStreamTestCase extends \PHPUnit_Framework_TestCase
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
        $this->handle              = fopen(__DIR__ . '/test_read.txt', 'r');
        $this->resourceInputStream = new TestResourceInputStream($this->handle);
    }

    /**
     * try to create an instance with an invalid handle
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
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
        $this->assertEquals(13, $this->resourceInputStream->bytesLeft());
        $this->assertEquals("foobarbaz\njjj", $this->resourceInputStream->read());
        $this->assertEquals(0, $this->resourceInputStream->bytesLeft());
    }

    /**
     * read data from resource
     *
     * @test
     */
    public function readBytes()
    {
        $this->assertEquals(13, $this->resourceInputStream->bytesLeft());
        $this->assertEquals('foobar', $this->resourceInputStream->read(6));
        $this->assertEquals(7, $this->resourceInputStream->bytesLeft());
    }

    /**
     * read data from resource
     *
     * @test
     */
    public function readLine()
    {
        $this->assertEquals(13, $this->resourceInputStream->bytesLeft());
        $this->assertEquals('foobarbaz', $this->resourceInputStream->readLine());
        $this->assertEquals(3, $this->resourceInputStream->bytesLeft());
    }

    /**
     * check end of file pointer
     *
     * @test
     */
    public function endOfFile()
    {
        $this->assertFalse($this->resourceInputStream->eof());
        $this->resourceInputStream->read();
        $this->assertTrue($this->resourceInputStream->eof());
        $this->assertEquals('', $this->resourceInputStream->read());
        $this->assertEquals('', $this->resourceInputStream->readLine());
    }

    /**
     * check end of file pointer
     *
     * @test
     */
    public function endOfFileReadLine()
    {
        $this->assertFalse($this->resourceInputStream->eof());
        $this->assertEquals('foobarbaz', $this->resourceInputStream->readLine());
        $this->assertFalse($this->resourceInputStream->eof());
        $this->assertEquals('jjj', $this->resourceInputStream->readLine());
        $this->assertTrue($this->resourceInputStream->eof());
        $this->assertEquals('', $this->resourceInputStream->readLine());
    }

    /**
     * trying to read fails after resource was closed
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
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
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
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
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
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
     * @expectedException  net\stubbles\lang\exception\IOException
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
     * @expectedException  net\stubbles\lang\exception\IOException
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
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function bytesLeftAfterCloseFromOutsite()
    {
        fclose($this->handle);
        $this->resourceInputStream->bytesLeft();
    }
}
?>