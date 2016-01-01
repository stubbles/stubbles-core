<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles;
use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\lang\*().
 *
 * @since  3.1.0
 * @group  lang
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @since  3.4.2
     */
    public function lastErrorMessageShouldBeNullByDefault()
    {
        assert(lastErrorMessage(), equals(Result::of(null)));
    }

    /**
     * @test
     * @since  3.4.2
     */
    public function lastErrorMessageShouldContainLastError()
    {
        @file_get_contents(__DIR__ . '/doesNotExist.txt');
        assert(
                lastErrorMessage()->value(),
                equals('file_get_contents(' . __DIR__ . '/doesNotExist.txt): failed to open stream: No such file or directory')
        );
    }
}
