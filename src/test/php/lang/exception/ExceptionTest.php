<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\exception;
use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\lang\exception\Exception.
 *
 * @group  lang
 * @group  lang_exception
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function toStringResult()
    {
        $exception = new Exception('message');
        assert(
                (string) $exception,
                equals("stubbles\lang\\exception\Exception {\n    message(string): message\n    file(string): " . __FILE__ . "\n    line(integer): " . $exception->getLine() . "\n    code(integer): 0\n    stacktrace(string): " . $exception->getTraceAsString() . "\n}\n")
        );
    }
}
