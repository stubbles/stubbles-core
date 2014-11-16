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
 * Test for stubbles\streams\InputStreamIterator.
 *
 * @group  streams
 * @since  5.2.0
 */
class InputStreamIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  InputStreamIterator
     */
    private $inputStreamIterator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->inputStreamIterator = new InputStreamIterator(
                new MemoryInputStream("foo\nbar\n\baz\n")
        );
    }

    /**
     * @test
     */
    public function iteration()
    {
        $expectedLineNumber = 1;
        $expectedLine = [1 => 'foo', 2 => 'bar', 3 => 'baz', 4 => ''];
        foreach ($this->inputStreamIterator as $lineNumber => $line) {
            $this->assertEquals($expectedLineNumber, $lineNumber);
            $this->assertEquals($expectedLine[$expectedLineNumber], $line);
            $expectedLineNumber++;
        }
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function createFromNonSeekableInputStreamThrowsIllegalArgumentException()
    {
        new InputStreamIterator($this->getMock('stubbles\streams\InputStream'));
    }
}
