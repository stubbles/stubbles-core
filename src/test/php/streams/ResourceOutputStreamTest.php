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
class TestResourceOutputStream extends ResourceOutputStream
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
 * Test for stubbles\streams\ResourceOutputStream.
 *
 * @group  streams
 */
class ResourceOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  TestResourceOutputStream
     */
    protected $resourceOutputStream;
    /**
     * the handle
     *
     * @type  resource
     */
    protected $handle;
    /**
     * root directory
     *
     * @type   org\bovigo\vfs\vfsDirectory
     */
    protected $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root                 = vfsStream::setup();
        $this->handle               = fopen(vfsStream::url('root/test_write.txt'), 'w');
        $this->resourceOutputStream = new TestResourceOutputStream($this->handle);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function invalidHandleThrowsIllegalArgumentException()
    {
        new TestResourceOutputStream('invalid');
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function writeToClosedStreamThrowsIllegalStateException()
    {
        $this->resourceOutputStream->close();
        $this->resourceOutputStream->write('foobarbaz');
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function writeLineToClosedStreamThrowsIllegalStateException()
    {
        $this->resourceOutputStream->close();
        $this->resourceOutputStream->writeLine('foobarbaz');
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function writeToExternalClosedStreamThrowsIOException()
    {
        fclose($this->handle);
        $this->resourceOutputStream->write('foobarbaz');
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function writeLineToExternalClosedStreamThrowsIOException()
    {
        fclose($this->handle);
        $this->resourceOutputStream->writeLine('foobarbaz');
    }

    /**
     * @test
     */
    public function writePassesBytesIntoStream()
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $resourceOutputStream = new TestResourceOutputStream(fopen(vfsStream::url('root/test.txt'), 'w'));
        $this->assertEquals(9, $resourceOutputStream->write('foobarbaz'));
        $this->assertEquals('foobarbaz', $file->getContent());
    }

    /**
     * @test
     */
    public function writeLinePassesBytesWithLinebreakIntoStream()
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $resourceOutputStream = new TestResourceOutputStream(fopen(vfsStream::url('root/test.txt'), 'w'));
        $this->assertEquals(11, $resourceOutputStream->writeLine('foobarbaz'));
        $this->assertEquals("foobarbaz\r\n", $file->getContent());
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesPassesBytesWithLinebreakIntoStream()
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $resourceOutputStream = new TestResourceOutputStream(fopen(vfsStream::url('root/test.txt'), 'w'));
        $this->assertEquals(15, $resourceOutputStream->writeLines(['foo', 'bar', 'baz']));
        $this->assertEquals("foo\r\nbar\r\nbaz\r\n", $file->getContent());
    }
}
