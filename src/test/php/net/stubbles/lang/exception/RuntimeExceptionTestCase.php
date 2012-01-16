<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\exception;
/**
 * Helper class for equal() tests.
 */
class stub1RuntimeException extends RuntimeException
{
    // intentionally empty
}
/**
 * Helper class for equal() tests.
 */
class stub2RuntimeException extends RuntimeException
{
    // intentionally empty
}
/**
 * Tests for net\stubbles\lang\exception\RuntimeException.
 *
 * @group  lang
 * @group  lang_exception
 */
class RuntimeExceptionTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to be used for tests
     *
     * @type  RuntimeException
     */
    protected $runtimeException1;
    /**
     * instance 2 to be used for tests
     *
     * @type  RuntimeException
     */
    protected $runtimeException2;
    /**
     * instance 3 to be used for tests
     *
     * @type  RuntimeException
     */
    protected $runtimeException3;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->runtimeException1 = new stub1RuntimeException('message');
        $this->runtimeException2 = new stub2RuntimeException('message');
        $this->runtimeException3 = new RuntimeException('message');
    }

    /**
     * @test
     */
    public function getClassReturnsReflectorForClass()
    {
        $refObject = $this->runtimeException3->getClass();
        $this->assertInstanceOf('net\stubbles\lang\reflect\ReflectionObject', $refObject);
        $this->assertEquals('net\stubbles\lang\exception\RuntimeException', $refObject->getName());
    }

    /**
     * @test
     */
    public function getClassNameReturnsFullQualifiedClassNameOfClass()
    {
        $this->assertEquals('net\stubbles\lang\exception\RuntimeException',
                            $this->runtimeException3->getClassName()
        );
    }

    /**
     * @test
     */
    public function classInstanceIsEqualToItself()
    {
        $this->assertTrue($this->runtimeException1->equals($this->runtimeException1));
        $this->assertTrue($this->runtimeException2->equals($this->runtimeException2));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToInstanceOfOtherClass()
    {
        $this->assertFalse($this->runtimeException1->equals($this->runtimeException2));
        $this->assertFalse($this->runtimeException2->equals($this->runtimeException1));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToOtherInstanceOfSameClass()
    {
        $this->assertFalse($this->runtimeException1->equals(new stub1RuntimeException('message')));
        $this->assertFalse($this->runtimeException2->equals(new stub2RuntimeException('message')));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToString()
    {
        $this->assertFalse($this->runtimeException1->equals('foo'));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToNumber()
    {
        $this->assertFalse($this->runtimeException1->equals(6));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToBooleanTrue()
    {
        $this->assertFalse($this->runtimeException1->equals(true));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToBooleanFalse()
    {
        $this->assertFalse($this->runtimeException1->equals(false));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToNull()
    {
        $this->assertFalse($this->runtimeException1->equals(null));
    }

    /**
     * @test
     */
    public function toStringResult()
    {
        $this->assertEquals("net\stubbles\lang\exception\RuntimeException {\n    message(string): message\n    file(string): " . __FILE__ . "\n    line(integer): " . $this->runtimeException3->getLine() . "\n    code(integer): 0\n    stacktrace(string): " . $this->runtimeException3->getTraceAsString() . "\n}\n",
                            (string) $this->runtimeException3
        );
    }
}
?>