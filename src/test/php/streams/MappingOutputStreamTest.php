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
use stubbles\streams\memory\MemoryOutputStream;
/**
 * Test for stubbles\streams\MappingOutputStream.
 *
 * @group  streams
 * @since  4.0.0
 */
class MappingOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MappingOutputStream
     */
    private $mappingOutputStream;
    /**
     * underlying output stream which receives mapped data
     *
     * @type  MemoryOutputStream
     */
    private $memoryOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryOutputStream  = new MemoryOutputStream();
        $this->mappingOutputStream = new MappingOutputStream(
                $this->memoryOutputStream,
                function($data)
                {
                    return 'baz';
                }
        );
    }

    /**
     * @test
     */
    public function mapsDataBeforeWrite()
    {
        $this->mappingOutputStream->write('foo');
        $this->assertEquals('baz', $this->memoryOutputStream);
    }

    /**
     * @test
     */
    public function mapsDataBeforeWriteLine()
    {
        $this->mappingOutputStream->writeLine('foo');
        $this->assertEquals("baz\n", $this->memoryOutputStream);
    }

    /**
     * @test
     */
    public function mapsDataBeforeWriteLines()
    {
        $this->mappingOutputStream->writeLines(['foo', 'bar']);
        $this->assertEquals("baz\nbaz\n", $this->memoryOutputStream);
    }
}
