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

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
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
        assert($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     * @expectedException  stubbles\streams\StreamException
     * @expectedExceptionMessage  Can not open file doesNotExist with mode r: failed to open stream: No such file or directory
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
        assert($fileInputStream->readLine(), equals('foo'));
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
        assert($fileInputStream->tell(), equals(0));
        $fileInputStream->seek(2);
        assert($fileInputStream->tell(), equals(2));
        assert($fileInputStream->readLine(), equals('o'));
        $fileInputStream->seek(0, Seekable::SET);
        assert($fileInputStream->tell(), equals(0));
        assert($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function seek_CURRENT()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(1, Seekable::CURRENT);
        assert($fileInputStream->tell(), equals(1));
        assert($fileInputStream->readLine(), equals('oo'));
    }

    /**
     * @test
     */
    public function seek_END()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(-2, Seekable::END);
        assert($fileInputStream->tell(), equals(1));
        assert($fileInputStream->readLine(), equals('oo'));
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
