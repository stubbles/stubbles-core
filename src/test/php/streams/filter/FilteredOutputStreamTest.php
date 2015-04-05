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
use stubbles\streams\memory\MemoryOutputStream;
/**
 * Test for stubbles\streams\filter\FilteredOutputStream.
 *
 * @group  streams
 * @group  streams_filter
 */
class FilteredOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\filter\FilteredOutputStream
     */
    private $filteredOutputStream;
    /**
     * decorated input stream
     *
     * @type  \stubbles\streams\memory\MemoryOutputStream
     */
    private $memory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memory = new MemoryOutputStream();
        $this->filteredOutputStream = new FilteredOutputStream(
                $this->memory,
                Predicate::castFrom(
                        function($value)
                        {
                            return 'foo' === $value;
                        }
                )
        );
    }

    /**
     * @test
     */
    public function dataPassingTheFilterShouldBeWritten()
    {
        $this->assertEquals(3, $this->filteredOutputStream->write('foo'));
        $this->assertEquals('foo', $this->memory->buffer());
    }

    /**
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWritten()
    {
        $this->assertEquals(0, $this->filteredOutputStream->write('bar'));
        $this->assertEquals('', $this->memory->buffer());
    }

    /**
     * @test
     */
    public function dataPassingTheFilterShouldBeWrittenAsLine()
    {
        $this->assertEquals(4, $this->filteredOutputStream->writeLine('foo'));
        $this->assertEquals("foo\n", $this->memory->buffer());
    }

    /**
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWrittenAsLine()
    {
        $this->assertEquals(0, $this->filteredOutputStream->writeLine('bar'));
        $this->assertEquals('', $this->memory->buffer());
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesProcessesOnlyLinesSatisfyingFilter()
    {
        $this->assertEquals(4, $this->filteredOutputStream->writeLines(['foo', 'bar']));
        $this->assertEquals("foo\n", $this->memory->buffer());
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  4.0.0
     */
    public function createInstanceWithNoStreamFilterAndNoPredicateAndNoCallableThrowsIllegalArgumentException()
    {
        new FilteredOutputStream($this->memory, new \stdClass());
    }
}
