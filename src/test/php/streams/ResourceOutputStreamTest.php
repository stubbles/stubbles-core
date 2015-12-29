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
use function bovigo\assert\predicate\equals;
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
        assert($resourceOutputStream->write('foobarbaz'), equals(9));
        assert($file->getContent(), equals('foobarbaz'));
    }

    /**
     * @test
     */
    public function writeLinePassesBytesWithLinebreakIntoStream()
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $resourceOutputStream = new TestResourceOutputStream(fopen(vfsStream::url('root/test.txt'), 'w'));
        assert($resourceOutputStream->writeLine('foobarbaz'), equals(11));
        assert($file->getContent(), equals("foobarbaz\r\n"));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesPassesBytesWithLinebreakIntoStream()
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $resourceOutputStream = new TestResourceOutputStream(fopen(vfsStream::url('root/test.txt'), 'w'));
        assert(
                $resourceOutputStream->writeLines(['foo', 'bar', 'baz']),
                equals(15)
        );
        assert($file->getContent(), equals("foo\r\nbar\r\nbaz\r\n"));
    }
}
