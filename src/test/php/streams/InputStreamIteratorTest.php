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
     * @test
     */
    public function canIterateOverSeekableInputStream()
    {
        $expectedLineNumber = 1;
        $expectedLine = [1 => 'foo', 2 => 'bar', 3 => 'baz', 4 => ''];
        foreach (linesOf(new MemoryInputStream("foo\nbar\nbaz\n")) as $lineNumber => $line) {
            $this->assertEquals($expectedLineNumber, $lineNumber);
            $this->assertEquals($expectedLine[$expectedLineNumber], $line);
            $expectedLineNumber++;
        }
    }

    /**
     * @test
     */
    public function canRewindSeekableInputStream()
    {
        $lines = linesOf(new MemoryInputStream("foo\nbar\nbaz\n"));
        foreach ($lines as $lineNumber => $line) {
            // do nothing
        }

        $expectedLineNumber = 1;
        $expectedLine = [1 => 'foo', 2 => 'bar', 3 => 'baz', 4 => ''];
        foreach ($lines as $lineNumber => $line) {
            $this->assertEquals($expectedLineNumber, $lineNumber);
            $this->assertEquals($expectedLine[$expectedLineNumber], $line);
            $expectedLineNumber++;
        }
    }

    /**
     * @test
     */
    public function canIterateOverNonSeekableInputStream()
    {
        $inputStream = $this->getMock('stubbles\streams\InputStream');
        $inputStream->method('readLine')
                ->will($this->onConsecutiveCalls('foo', 'bar', 'baz'));
        $inputStream->method('eof')
                ->will($this->onConsecutiveCalls(false, false, false, true));
        $expectedLineNumber = 1;
        $expectedLine = [1 => 'foo', 2 => 'bar', 3 => 'baz', 4 => ''];
        foreach (linesOf($inputStream) as $lineNumber => $line) {
            $this->assertEquals($expectedLineNumber, $lineNumber);
            $this->assertEquals($expectedLine[$expectedLineNumber], $line);
            $expectedLineNumber++;
        }
    }

    /**
     * @test
     */
    public function canNotRewindNonSeekableInputStream()
    {
        $inputStream = $this->getMock('stubbles\streams\InputStream');
        $inputStream->method('readLine')
                ->will($this->onConsecutiveCalls('foo', 'bar', 'baz'));
        $inputStream->method('eof')
                ->will($this->onConsecutiveCalls(false, false, false, true, true, true));
        $lines = linesOf($inputStream);
        foreach ($lines as $lineNumber => $line) {
            // do nothing
        }

        $count = 0;
        foreach (linesOf($inputStream) as $lineNumber => $line) {
            $count++;
        }

        $this->assertEquals(0, $count);
    }
}
