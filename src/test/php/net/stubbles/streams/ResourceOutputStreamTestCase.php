<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams;
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
 * Test for net\stubbles\streams\ResourceOutputStream.
 *
 * @group  streams
 */
class ResourceOutputStreamTestCase extends \PHPUnit_Framework_TestCase
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
     * set up test environment
     */
    public function setUp()
    {
        $this->handle               = fopen(__DIR__ . '/test_write.txt', 'w');
        $this->resourceOutputStream = new TestResourceOutputStream($this->handle);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidHandleThrowsIllegalArgumentException()
    {
        $resourceOutputStream = new TestResourceOutputStream('invalid');
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function writeToClosedStreamThrowsIllegalStateException()
    {
        $this->resourceOutputStream->close();
        $this->resourceOutputStream->write('foobarbaz');
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalStateException
     */
    public function writeLineToClosedStreamThrowsIllegalStateException()
    {
        $this->resourceOutputStream->close();
        $this->resourceOutputStream->writeLine('foobarbaz');
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IOException
     */
    public function writeToExternalClosedStreamThrowsIOException()
    {
        fclose($this->handle);
        $this->resourceOutputStream->write('foobarbaz');
    }

    /**
     * try to write to an already closed stream
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\IOException
     */
    public function writeLineToExternalClosedStreamThrowsIOException()
    {
        fclose($this->handle);
        $this->resourceOutputStream->writeLine('foobarbaz');
    }

    /**
     * write some stuff into stream
     *
     * @test
     */
    public function write()
    {
        $root = vfsStream::setup('home');
        $file = vfsStream::newFile('test.txt')->at($root);
        $resourceOutputStream = new TestResourceOutputStream(fopen(vfsStream::url('home/test.txt'), 'w'));
        $this->assertEquals(9, $resourceOutputStream->write('foobarbaz'));
        $this->assertEquals('foobarbaz', $file->getContent());
    }

    /**
     * write some stuff into stream
     *
     * @test
     */
    public function writeLine()
    {
        $root = vfsStream::setup('home');
        $file = vfsStream::newFile('test.txt')->at($root);
        $resourceOutputStream = new TestResourceOutputStream(fopen(vfsStream::url('home/test.txt'), 'w'));
        $this->assertEquals(11, $resourceOutputStream->writeLine('foobarbaz'));
        $this->assertEquals("foobarbaz\r\n", $file->getContent());
    }
}
?>