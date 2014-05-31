<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\file;
use org\bovigo\vfs\vfsStream;
/**
 * Test for stubbles\streams\file\FileOutputStream.
 *
 * @group  streams
 * @group  streams_file
 */
class FileOutputStreamTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * the file url used in the tests
     *
     * @type  string
     */
    protected $fileUrl;

    /**
     * set up test environment
     */
    public function setUp()
    {
        vfsStream::setup('home');
        $this->fileUrl = vfsStream::url('home/test.txt');
    }

    /**
     * construct with string as argument
     *
     * @test
     */
    public function constructWithString()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = new FileOutputStream($this->fileUrl);
        $this->assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * @test
     */
    public function constructWithStringDelayed()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = new FileOutputStream($this->fileUrl, 'wb', true);
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertTrue(file_exists($this->fileUrl));
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function constructWithStringFailsAndThrowsIOException()
    {
        vfsStream::newFile('test.txt', 0000)->at(vfsStream::setup());
        new FileOutputStream($this->fileUrl, 'r');
    }

    /**
     * @test
     */
    public function constructWithResource()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = new FileOutputStream(fopen($this->fileUrl, 'wb'));
        $this->assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function constructWithIllegalResource()
    {
        if (extension_loaded('gd') === false) {
            $this->markTestSkipped('No known extension with other resource type available.');
        }

        new FileOutputStream(imagecreate(2, 2));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function constructWithIllegalArgument()
    {
        new FileOutputStream(0);
    }
}
