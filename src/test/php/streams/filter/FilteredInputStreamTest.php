<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\filter;
use stubbles\predicate\Predicate;
use stubbles\streams\memory\MemoryInputStream;
/**
 * Test for stubbles\streams\filter\FilteredInputStream.
 *
 * @group  streams
 * @group  streams_filter
 */
class FilteredInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FilteredInputStream
     */
    private $filteredInputStream;
    /**
     * mocked input stream
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInputStream     = new MemoryInputStream("foo\nbar");
        $this->filteredInputStream = new FilteredInputStream(
                $this->mockInputStream,
                Predicate::castFrom(
                        function($value)
                        {
                            return 'bar' === $value;
                        }
                )
        );
    }

    /**
     * @test
     */
    public function readReturnsEmptyStringIfChunkIsFiltered()
    {
        assertEquals('', $this->filteredInputStream->read());
    }

    /**
     * @test
     */
    public function readLineReturnsUnfilteredLinesOnly()
    {
        assertEquals('bar', $this->filteredInputStream->readLine());
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  4.0.0
     */
    public function createInstanceWithNoStreamFilterAndNoPredicateAndNoCallableThrowsIllegalArgumentException()
    {
        new FilteredInputStream($this->mockInputStream, new \stdClass());
    }
}
