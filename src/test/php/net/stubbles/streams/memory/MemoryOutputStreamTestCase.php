<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams\memory;
/**
 * Test for net\stubbles\streams\memory\MemoryOutputStream.
 *
 * @group  streams
 * @group  streams_memory
 */
class MemoryOutputStreamTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * the file url used in the tests
     *
     * @type  MemoryOutputStream
     */
    protected $memoryOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryOutputStream = new MemoryOutputStream();
    }

    /**
     * write() puts data into buffer
     *
     * @test
     */
    public function write()
    {
        $this->assertEquals('', $this->memoryOutputStream->getBuffer());
        $this->assertEquals(5, $this->memoryOutputStream->write('hello'));
        $this->assertEquals('hello', $this->memoryOutputStream->getBuffer());
    }

    /**
     * writeLine() puts data into buffer
     *
     * @test
     */
    public function writeLine()
    {
        $this->assertEquals('', $this->memoryOutputStream->getBuffer());
        $this->assertEquals(6, $this->memoryOutputStream->writeLine('hello'));
        $this->assertEquals("hello\n", $this->memoryOutputStream->getBuffer());
    }

    /**
     * close() does nothing
     *
     * @test
     */
    public function close()
    {
        $this->memoryOutputStream->close();
    }
}
?>