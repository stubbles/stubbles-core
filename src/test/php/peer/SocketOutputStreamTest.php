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
        assertEquals(3, $this->socketOutputStream->write('foo'));
        assertEquals('foo', $this->file->getContent());
    }

    /**
     * @test
     */
    public function writeLinePassesBytesToSocketWithLinebreak()
    {
        assertEquals(5, $this->socketOutputStream->writeLine('foo'));
        assertEquals("foo\r\n", $this->file->getContent());
    }

    /**
     * @test
     */
    public function writeLinesPassesBytesToSocketWithLinebreak()
    {
        assertEquals(
                10,
                $this->socketOutputStream->writeLines(['foo', 'bar'])
        );
        assertEquals("foo\r\nbar\r\n", $this->file->getContent());
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
