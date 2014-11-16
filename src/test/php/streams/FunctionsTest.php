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
                               ->withContent("some\nContent\n")
                               ->at($root);
    }

    /**
     * @test
     */
    public function linesOfReturnsSequence()
    {
        $this->assertInstanceOf(
                'stubbles\lang\Sequence',
                linesOf($this->file->url())
        );
    }
}
