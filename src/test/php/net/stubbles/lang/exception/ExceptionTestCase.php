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
class stub1Exception extends Exception
{
    // intentionally empty
}
/**
 * Helper class for equal() tests.
 */
class stub2Exception extends Exception
{
    // intentionally empty
}
/**
 * Tests for net\stubbles\lang\exception\Exception.
 *
 * @group  lang
 * @group  lang_exception
 */
class ExceptionTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to be used for tests
     *
     * @type  Exception
     */
    protected $exception1;
    /**
     * instance 2 to be used for tests
     *
     * @type  Exception
     */
    protected $exception2;
    /**
     * instance 3 to be used for tests
     *
     * @type  Exception
     */
    protected $exception3;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->exception1 = new stub1Exception('message');
        $this->exception2 = new stub2Exception('message');
        $this->exception3 = new Exception('message');
    }

    /**
     * @test
     */
    public function getClassReturnsReflectorForClass()
    {
        $refObject = $this->exception3->getClass();
        $this->assertInstanceOf('net\stubbles\lang\reflect\ReflectionObject', $refObject);
        $this->assertEquals('net\stubbles\lang\exception\Exception', $refObject->getName());
    }

    /**
     * @test
     */
    public function getClassNameReturnsFullQualifiedClassNameOfClass()
    {
        $this->assertEquals('net\stubbles\lang\exception\Exception',
                            $this->exception3->getClassName()
        );
    }

    /**
     * @test
     */
    public function classInstanceIsEqualToItself()
    {
        $this->assertTrue($this->exception1->equals($this->exception1));
        $this->assertTrue($this->exception2->equals($this->exception2));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToInstanceOfOtherClass()
    {
        $this->assertFalse($this->exception1->equals($this->exception2));
        $this->assertFalse($this->exception2->equals($this->exception1));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToOtherInstanceOfSameClass()
    {
        $this->assertFalse($this->exception1->equals(new stub1Exception('message')));
        $this->assertFalse($this->exception2->equals(new stub2Exception('message')));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToString()
    {
        $this->assertFalse($this->exception1->equals('foo'));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToNumber()
    {
        $this->assertFalse($this->exception1->equals(6));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToBooleanTrue()
    {
        $this->assertFalse($this->exception1->equals(true));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToBooleanFalse()
    {
        $this->assertFalse($this->exception1->equals(false));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToNull()
    {
        $this->assertFalse($this->exception1->equals(null));
    }

    /**
     * @test
     */
    public function toStringResult()
    {
        $this->assertEquals("net\stubbles\lang\exception\Exception {\n    message(string): message\n    file(string): " . __FILE__ . "\n    line(integer): " . $this->exception3->getLine() . "\n    code(integer): 0\n    stacktrace(string): " . $this->exception3->getTraceAsString() . "\n}\n",
                            (string) $this->exception3
        );
    }
}
?>