<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\memory;
/**
 * Test for stubbles\streams\memory\MemoryOutputStream.
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
     * @test
     */
    public function writeWritesBytesIntoBuffer()
    {
        $this->assertEquals('', $this->memoryOutputStream->getBuffer());
        $this->assertEquals(5, $this->memoryOutputStream->write('hello'));
        $this->assertEquals('hello', $this->memoryOutputStream->getBuffer());
    }

    /**
     * @test
     */
    public function writeLineWritesBytesIntoBuffer()
    {
        $this->assertEquals('', $this->memoryOutputStream->getBuffer());
        $this->assertEquals(6, $this->memoryOutputStream->writeLine('hello'));
        $this->assertEquals("hello\n", $this->memoryOutputStream->getBuffer());
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesWritesBytesIntoBuffer()
    {
        $this->assertEquals('', $this->memoryOutputStream->getBuffer());
        $this->assertEquals(12, $this->memoryOutputStream->writeLines(array('hello', 'world')));
        $this->assertEquals("hello\nworld\n", $this->memoryOutputStream->getBuffer());
    }

    /**
     * @test
     */
    public function closeDoesNothing()
    {
        $this->assertNull($this->memoryOutputStream->close());
    }
}
