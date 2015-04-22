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
use bovigo\callmap;
use bovigo\callmap\NewInstance;
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
            assertEquals($expectedLineNumber, $lineNumber);
            assertEquals($expectedLine[$expectedLineNumber], $line);
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
            assertEquals($expectedLineNumber, $lineNumber);
            assertEquals($expectedLine[$expectedLineNumber], $line);
            $expectedLineNumber++;
        }
    }

    /**
     * @test
     */
    public function canIterateOverNonSeekableInputStream()
    {
        $inputStream = NewInstance::of('stubbles\streams\InputStream')
                ->mapCalls(
                        ['readLine' => callmap\onConsecutiveCalls('foo', 'bar', 'baz'),
                         'eof'      => callmap\onConsecutiveCalls(false, false, false, true)
                        ]
        );
        $expectedLineNumber = 1;
        $expectedLine = [1 => 'foo', 2 => 'bar', 3 => 'baz', 4 => ''];
        foreach (linesOf($inputStream) as $lineNumber => $line) {
            assertEquals($expectedLineNumber, $lineNumber);
            assertEquals($expectedLine[$expectedLineNumber], $line);
            $expectedLineNumber++;
        }
    }

    /**
     * @test
     */
    public function canNotRewindNonSeekableInputStream()
    {
        $inputStream = NewInstance::of('stubbles\streams\InputStream')
                ->mapCalls(
                        ['readLine' => callmap\onConsecutiveCalls('foo', 'bar', 'baz'),
                         'eof'      => callmap\onConsecutiveCalls(false, false, false, true, true)
                        ]
        );
        $lines = linesOf($inputStream);
        foreach ($lines as $lineNumber => $line) {
            // do nothing
        }

        $count = 0;
        foreach (linesOf($inputStream) as $lineNumber => $line) {
            $count++;
        }

        assertEquals(0, $count);
    }
}
