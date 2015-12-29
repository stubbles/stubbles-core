<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer;
use org\bovigo\vfs\vfsStream;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\peer\SocketOutputStream.
 *
 * @group  peer
 */
class SocketOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\peer\SocketOutputStream
     */
    private $socketOutputStream;
    /**
     * mocked socket instance
     *
     * @type  \org\bovigo\vfs\vfsStreamFile
     */
    private $file;


    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->file = vfsStream::newFile('foo.txt')->at(vfsStream::setup());
        $this->socketOutputStream = new SocketOutputStream(
                new Stream(fopen($this->file->url(), 'wb+'))
        );
    }

    /**
     * @test
     */
    public function writePassesBytesToSocket()
    {
        assert($this->socketOutputStream->write('foo'), equals(3));
        assert($this->file->getContent(), equals('foo'));
    }

    /**
     * @test
     */
    public function writeLinePassesBytesToSocketWithLinebreak()
    {
        assert($this->socketOutputStream->writeLine('foo'), equals(5));
        assert($this->file->getContent(), equals("foo\r\n"));
    }

    /**
     * @test
     */
    public function writeLinesPassesBytesToSocketWithLinebreak()
    {
        assert(
                $this->socketOutputStream->writeLines(['foo', 'bar']),
                equals(10)
        );
        assert($this->file->getContent(), equals("foo\r\nbar\r\n"));
    }

    /**
     * @test
     * @expectedException  LogicException
     * @since  6.0.0
     */
    public function writeOnClosedStreamThrowsLogicException()
    {
        $this->socketOutputStream->close();
        $this->socketOutputStream->write('foo');
    }

    /**
     * @test
     * @expectedException  LogicException
     * @since  6.0.0
     */
    public function writeLinesOnClosedStreamThrowsLogicException()
    {
        $this->socketOutputStream->close();
        $this->socketOutputStream->writeLine('foo');
    }

    /**
     * @test
     * @expectedException  LogicException
     * @since  6.0.0
     */
    public function writeLineOnClosedStreamThrowsLogicException()
    {
        $this->socketOutputStream->close();
        $this->socketOutputStream->writeLines(['foo', 'bar']);
    }
}
