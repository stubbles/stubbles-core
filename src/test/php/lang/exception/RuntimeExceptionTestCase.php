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
 * Tests for stubbles\lang\exception\RuntimeException.
 *
 * @group  lang
 * @group  lang_exception
 */
class RuntimeExceptionTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance 3 to be used for tests
     *
     * @type  RuntimeException
     */
    private $runtimeException;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->runtimeException = new RuntimeException('message');
    }

    /**
     * @test
     */
    public function toStringResult()
    {
        $this->assertEquals("stubbles\lang\\exception\RuntimeException {\n    message(string): message\n    file(string): " . __FILE__ . "\n    line(integer): " . $this->runtimeException->getLine() . "\n    code(integer): 0\n    stacktrace(string): " . $this->runtimeException->getTraceAsString() . "\n}\n",
                            (string) $this->runtimeException
        );
    }
}
