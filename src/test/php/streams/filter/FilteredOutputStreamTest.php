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

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\predicate\equals;
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
        assert($this->filteredOutputStream->write('foo'), equals(3));
        assert($this->memory->buffer(), equals('foo'));
    }

    /**
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWritten()
    {
        assert($this->filteredOutputStream->write('bar'), equals(0));
        assertEmptyString($this->memory->buffer());
    }

    /**
     * @test
     */
    public function dataPassingTheFilterShouldBeWrittenAsLine()
    {
        assert($this->filteredOutputStream->writeLine('foo'), equals(4));
        assert($this->memory->buffer(), equals("foo\n"));
    }

    /**
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWrittenAsLine()
    {
        assert($this->filteredOutputStream->writeLine('bar'), equals(0));
        assertEmptyString($this->memory->buffer());
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesProcessesOnlyLinesSatisfyingFilter()
    {
        assert($this->filteredOutputStream->writeLines(['foo', 'bar']), equals(4));
        assert($this->memory->buffer(), equals("foo\n"));
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
