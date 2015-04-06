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
use stubbles\streams\Seekable;
use org\bovigo\vfs\vfsStream;
/**
 * Test for stubbles\streams\file\FileInputStream.
 *
 * @group  streams
 */
class FileInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $root = vfsStream::setup('home');
        vfsStream::newFile('test.txt')->at($root)->withContent('foo');
    }

    /**
     * @test
     */
    public function constructWithString()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        assertEquals('foo', $fileInputStream->readLine());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function constructWithStringFailsAndThrowsIOException()
    {
        new FileInputStream('doesNotExist', 'r');
    }

    /**
     * @test
     */
    public function constructWithResource()
    {
        $fileInputStream = new FileInputStream(fopen(vfsStream::url('home/test.txt'), 'rb'));
        assertEquals('foo', $fileInputStream->readLine());
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

        new FileInputStream(imagecreate(2, 2));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function constructWithIllegalArgument()
    {
        new FileInputStream(0);
    }

    /**
     * @test
     */
    public function seek_SET()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        assertEquals(0, $fileInputStream->tell());
        $fileInputStream->seek(2);
        assertEquals(2, $fileInputStream->tell());
        assertEquals('o', $fileInputStream->readLine());
        $fileInputStream->seek(0, Seekable::SET);
        assertEquals(0, $fileInputStream->tell());
        assertEquals('foo', $fileInputStream->readLine());
    }

    /**
     * @test
     */
    public function seek_CURRENT()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(1, Seekable::CURRENT);
        assertEquals(1, $fileInputStream->tell());
        assertEquals('oo', $fileInputStream->readLine());
    }

    /**
     * @test
     */
    public function seek_END()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(-2, Seekable::END);
        assertEquals(1, $fileInputStream->tell());
        assertEquals('oo', $fileInputStream->readLine());
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function seekOnClosedStreamFailsThrowsIllegalStateException()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->close();
        $fileInputStream->seek(3);
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function tellOnClosedStreamThrowsIllegalStateException()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->close();
        $fileInputStream->tell();
    }
}
