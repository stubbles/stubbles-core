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

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
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
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function invalidHandleThrowsIllegalArgumentException()
    {
        new TestResourceInputStream('invalid');
    }

    /**
     * @test
     */
    public function hasBytesLeftWhenOpenedAtStart()
    {
        assert($this->resourceInputStream->bytesLeft(), equals(13));
    }

    /**
     * @test
     */
    public function isNotAtEofWhenOpenedAtStart()
    {
        assertFalse($this->resourceInputStream->eof());
    }

    /**
     * @test
     */
    public function hasNoBytesLeftWhenEverythingRead()
    {
        $this->resourceInputStream->read();
        assert($this->resourceInputStream->bytesLeft(), equals(0));
    }

    /**
     * @test
     */
    public function read()
    {
        assert($this->resourceInputStream->read(), equals("foobarbaz\njjj"));
    }

    /**
     * @test
     */
    public function readBytes()
    {
        assert($this->resourceInputStream->read(6), equals('foobar'));
    }

    /**
     * @test
     */
    public function hasBytesLeftWhenNotEverythingRead()
    {
        $this->resourceInputStream->read(6);
        assert($this->resourceInputStream->bytesLeft(), equals(7));
    }

    /**
     * @test
     */
    public function readLine()
    {
        assert($this->resourceInputStream->readLine(), equals('foobarbaz'));
    }

    /**
     * @test
     */
    public function hasReachedEofWhenEverythingRead()
    {
        $this->resourceInputStream->read();
        assertTrue($this->resourceInputStream->eof());
    }

    /**
     * @test
     */
    public function readAfterEofReturnsEmptyString()
    {
        $this->resourceInputStream->read();
        assert($this->resourceInputStream->read(), equals(''));
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function readAfterCloseFails()
    {
        $this->resourceInputStream->close();
        $this->resourceInputStream->read();
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function readLineAfterCloseFails()
    {
        $this->resourceInputStream->close();
        $this->resourceInputStream->readLine();
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function bytesLeftAfterCloseFails()
    {
        $this->resourceInputStream->close();
        $this->resourceInputStream->bytesLeft();
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function readAfterCloseFromOutsite()
    {
        fclose($this->handle);
        $this->resourceInputStream->read();
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function readLineAfterCloseFromOutsite()
    {
        fclose($this->handle);
        $this->resourceInputStream->readLine();
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function bytesLeftAfterCloseFromOutsite()
    {
        fclose($this->handle);
        $this->resourceInputStream->bytesLeft();
    }
}
