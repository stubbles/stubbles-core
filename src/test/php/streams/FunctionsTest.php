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
use org\bovigo\vfs\vfsStream;
use stubbles\lang\Sequence;
/**
 * Tests for stubbles\streams\*().
 *
 * @since  5.2.0
 * @group  streams
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    private $file;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $root       = vfsStream::setup();
        $this->file = vfsStream::newFile('test.txt')
                               ->withContent("foo\nfoo\n\n")
                               ->at($root);
    }

    /**
     * @test
     */
    public function linesOfReturnsSequence()
    {
        assertInstanceOf(
                Sequence::class,
                linesOf($this->file->url())
        );
    }

    /**
     * @test
     * @since  6.2.0
     */
    public function nonEmptyLinesOfReturnsNonEmptyLinesOnly()
    {
        foreach (nonEmptyLinesOf($this->file->url()) as $line) {
            assertEquals('foo', $line);
        }
    }
}
