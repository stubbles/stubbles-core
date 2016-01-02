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
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\predicate\equals;
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
     * @type  \stubbles\streams\filter\FilteredInputStream
     */
    private $filteredInputStream;
    /**
     * mocked input stream
     *
     * @type  \stubbles\streams\memory\MemoryInputStream
     */
    private $inputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->inputStream = new MemoryInputStream("foo\nbar");
        $this->filteredInputStream = new FilteredInputStream(
                $this->inputStream,
                function($value)
                {
                    return 'bar' === $value;
                }
        );
    }

    /**
     * @test
     */
    public function readReturnsEmptyStringIfChunkIsFiltered()
    {
        assertEmptyString($this->filteredInputStream->read());
    }

    /**
     * @test
     */
    public function readLineReturnsUnfilteredLinesOnly()
    {
        assert($this->filteredInputStream->readLine(), equals('bar'));
    }
}
