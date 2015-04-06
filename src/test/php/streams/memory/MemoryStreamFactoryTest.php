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
 * Test for stubbles\streams\memory\MemoryStreamFactory.
 *
 * @group  streams
 * @group  streams_memory
 */
class MemoryStreamFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MemoryStreamFactory
     */
    protected $memoryStreamFactory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryStreamFactory = new MemoryStreamFactory();
    }

    /**
     * @test
     */
    public function createInputStream()
    {
        $memoryInputStream = $this->memoryStreamFactory->createInputStream('buffer');
        assertInstanceOf('stubbles\streams\memory\MemoryInputStream', $memoryInputStream);
        assertEquals('buffer', $memoryInputStream->readLine());
    }

    /**
     * @test
     */
    public function createOutputStream()
    {
        assertInstanceOf('stubbles\streams\memory\MemoryOutputStream',
                                $this->memoryStreamFactory->createOutputStream('buffer')
        );
    }
}
