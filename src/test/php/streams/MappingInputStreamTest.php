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
use stubbles\streams\memory\MemoryInputStream;
/**
 * Test for stubbles\streams\MappingInputStream.
 *
 * @group  streams
 * @since  4.0.0
 */
class MappingInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MappingInputStream
     */
    private $mappingInputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mappingInputStream = new MappingInputStream(
                new MemoryInputStream("foo\nbar"),
                function($data)
                {
                    return 'baz';
                }
        );
    }

    /**
     * @test
     */
    public function mapsDataFromRead()
    {
        $this->assertEquals('baz', $this->mappingInputStream->read());
    }

    /**
     * @test
     */
    public function mapsDataFromReadLine()
    {
        $this->assertEquals('baz', $this->mappingInputStream->readLine());
    }
}
