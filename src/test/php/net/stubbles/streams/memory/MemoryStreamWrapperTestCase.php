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
/**
 * Test for et\stubbles\streams\memory\MemoryStreamWrapper.
 *
 * @group  streams
 * @group  streams_memory
 */
class MemoryStreamWrapperTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * url of test stream
     *
     * @type  string
     */
    protected $url = 'memory://foo';
    /**
     * file pointer
     *
     * @type  resource
     */
    protected $fp;

    /**
     * set up test environment
     */
    public function setUp()
    {
        MemoryStreamWrapper::register();
        file_put_contents($this->url, 'foo');
        $this->fp = fopen($this->url, 'rw');
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        if (null !== $this->fp) {
            fclose($this->fp);
        }

        unlink($this->url);
    }

    /**
     * read data from stream
     *
     * @test
     */
    public function read()
    {
        $this->assertFalse(feof($this->fp));
        $this->assertEquals('foo', fread($this->fp, 3));
        $this->assertTrue(feof($this->fp));
    }

    /**
     * write data to stream
     *
     * @test
     */
    public function write()
    {
        $this->assertEquals(5, fwrite($this->fp, 'hello'));
        fseek($this->fp, 0);
        $this->assertEquals('hello', fread($this->fp, 4096));
    }

    /**
     * seek in memory stream
     *
     * @test
     */
    public function seek_SET()
    {
        $this->assertEquals(0, ftell($this->fp));
        $this->assertEquals(0, fseek($this->fp, 2));
        $this->assertEquals(2, ftell($this->fp));
        $this->assertEquals('o', fread($this->fp, 1));
        $this->assertEquals(0, fseek($this->fp, 0, SEEK_SET));
        $this->assertEquals(0, ftell($this->fp));
        $this->assertEquals('foo', fread($this->fp, 3));
    }

    /**
     * seek in memory stream
     *
     * @test
     */
    public function seek_CURRENT()
    {
        $this->assertEquals(0, fseek($this->fp, 1, SEEK_CUR));
        $this->assertEquals(1, ftell($this->fp));
        $this->assertEquals('oo', fread($this->fp, 2));
    }

    /**
     * seek in memory stream
     *
     * @test
     */
    public function seek_END()
    {
        $this->assertEquals(0, fseek($this->fp, -2, SEEK_END));
        $this->assertEquals(1, ftell($this->fp));
        $this->assertEquals('oo', fread($this->fp, 2));
    }

    /**
     * unlink() removes buffer from memory
     *
     * @test
     */
    public function unlinkRemovesBufferFromMemory()
    {
        $this->assertTrue(file_exists($this->url));
        $this->assertTrue(unlink($this->url));
        $this->assertFalse(file_exists($this->url));
        $this->assertFalse(unlink($this->url));
    }

    /**
     * fstat() and stat() return the size of the buffer
     *
     * @test
     */
    public function statReturnsSizeOfBuffer()
    {
        $statData = fstat($this->fp);
        $this->assertEquals(3, $statData['size']);
        $statData = stat($this->url);
        $this->assertEquals(3, $statData['size']);
        $this->assertFalse(@stat('memory://doesNotExist'));
    }

    /**
     * trying to open a non existing file with mode r or r+ will fail
     *
     * @test
     */
    public function openNonExistingBufferWithModeRFails()
    {
        $this->assertFalse(@fopen('memory://doesNotExist', 'r'));
        $this->assertFalse(@fopen('memory://doesNotExist', 'rb'));
        $this->assertFalse(@fopen('memory://doesNotExist', 'r+'));
        $this->assertFalse(@fopen('memory://doesNotExist', 'rb+'));
    }

    /**
     * opening an existing file with mode r or r+ sets pointer to beginning of buffer
     *
     * @test
     */
    public function openExistingBufferWithModeRSetsPositionTo0()
    {
        $fp = fopen('memory://foo', 'r');
        $this->assertEquals(0, ftell($fp));
        $this->assertFalse(feof($fp));
        fclose($fp);
        $fp = fopen('memory://foo', 'rb');
        $this->assertEquals(0, ftell($fp));
        $this->assertFalse(feof($fp));
        fclose($fp);
        $fp = fopen('memory://foo', 'r+');
        $this->assertEquals(0, ftell($fp));
        $this->assertFalse(feof($fp));
        fclose($fp);
        $fp = fopen('memory://foo', 'rb+');
        $this->assertEquals(0, ftell($fp));
        $this->assertFalse(feof($fp));
        fclose($fp);
    }

    /**
     * opening a file with mode w or w+ sets pointer to beginning of buffer and truncates it
     *
     * @test
     */
    public function openBufferWithModeWSetsPositionTo0AndTruncatesBuffer()
    {
        $fp = fopen('memory://foo', 'w');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        $fp = fopen('memory://foo', 'wb');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        $fp = fopen('memory://foo', 'w+');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        $fp = fopen('memory://foo', 'wb+');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        $fp = fopen('memory://doesNotExist', 'w');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
        $fp = fopen('memory://doesNotExist', 'wb');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
        $fp = fopen('memory://doesNotExist', 'w+');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
        $fp = fopen('memory://doesNotExist', 'wb+');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
    }

    /**
     * opening a file with mode a or a+ sets pointer to end of buffer
     *
     * @test
     */
    public function openBufferWithModeASetsPositionToEndOfBuffer()
    {
        $fp = fopen('memory://foo', 'a');
        $this->assertEquals(3, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        $fp = fopen('memory://foo', 'ab');
        $this->assertEquals(3, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        $fp = fopen('memory://foo', 'a+');
        $this->assertEquals(3, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        $fp = fopen('memory://foo', 'ab+');
        $this->assertEquals(3, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        $fp = fopen('memory://doesNotExist', 'a');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
        $fp = fopen('memory://doesNotExist', 'ab');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
        $fp = fopen('memory://doesNotExist', 'a+');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
        $fp = fopen('memory://doesNotExist', 'ab+');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
    }

    /**
     * trying to open an existing file with mode x or x+ will fail
     *
     * @test
     */
    public function openExistingBufferWithModeXFails()
    {
        $this->assertFalse(@fopen('memory://foo', 'x'));
        $this->assertFalse(@fopen('memory://foo', 'xb'));
        $this->assertFalse(@fopen('memory://foo', 'x+'));
        $this->assertFalse(@fopen('memory://foo', 'xb+'));
    }

    /**
     * opening an existing file with mode r or r+ sets pointer to beginning of buffer
     *
     * @test
     */
    public function openNonExistingBufferWithModeXSetsPositionTo0()
    {
        $fp = fopen('memory://doesNotExist', 'x');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
        $fp = fopen('memory://doesNotExist', 'xb');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
        $fp = fopen('memory://doesNotExist', 'x+');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
        $fp = fopen('memory://doesNotExist', 'xb+');
        $this->assertEquals(0, ftell($fp));
        $this->assertTrue(feof($fp));
        fclose($fp);
        unlink('memory://doesNotExist');
    }

}
?>