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

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\file\FileOutputStream.
 *
 * @group  streams
 * @group  streams_file
 */
class FileOutputStreamTest extends \PHPUnit_Framework_TestCase
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
    public function constructWithStringCreatesFile()
    {
        new FileOutputStream($this->fileUrl);
        assertTrue(file_exists($this->fileUrl));
    }

    /**
     * @test
     */
    public function constructWithStringDelayedDoesNotCreateFile()
    {
        new FileOutputStream($this->fileUrl, 'wb', true);
        assertFalse(file_exists($this->fileUrl));
    }

    /**
     * construct with string as argument
     *
     * @test
     */
    public function constructWithString()
    {
        $fileOutputStream = new FileOutputStream($this->fileUrl);
        $fileOutputStream->write('foo');
        assert(file_get_contents($this->fileUrl), equals('foo'));
    }

    /**
     * @test
     */
    public function constructWithStringDelayedCreatesFileOnWrite()
    {
        $fileOutputStream = new FileOutputStream($this->fileUrl, 'wb', true);
        $fileOutputStream->write('foo');
        assertTrue(file_exists($this->fileUrl));
    }

    /**
     * @test
     * @expectedException  stubbles\streams\StreamException
     * @expectedExceptionMessage  Can not open file vfs://home/test.txt with mode r: failed to open stream: "org\bovigo\vfs\vfsStreamWrapper::stream_open" call failed
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
        $fileOutputStream = new FileOutputStream(fopen($this->fileUrl, 'wb'));
        $fileOutputStream->write('foo');
        assert(file_get_contents($this->fileUrl), equals('foo'));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
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
     * @expectedException  InvalidArgumentException
     */
    public function constructWithIllegalArgument()
    {
        new FileOutputStream(0);
    }
}
