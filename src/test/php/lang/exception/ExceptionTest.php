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
/**
 * Tests for stubbles\lang\exception\Exception.
 *
 * @group  lang
 * @group  lang_exception
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @type  Exception
     */
    private $exception;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->exception = new Exception('message');
    }

    /**
     * @test
     */
    public function toStringResult()
    {
        $this->assertEquals("stubbles\lang\\exception\Exception {\n    message(string): message\n    file(string): " . __FILE__ . "\n    line(integer): " . $this->exception->getLine() . "\n    code(integer): 0\n    stacktrace(string): " . $this->exception->getTraceAsString() . "\n}\n",
                            (string) $this->exception
        );
    }
}
