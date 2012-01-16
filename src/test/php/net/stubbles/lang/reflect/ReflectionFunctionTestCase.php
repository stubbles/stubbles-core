<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect;
/**
 * does not return anything
 *
 * @FunctionTest()
 */
function testWithOutParams()
{
    // intentionally empty
}
/**
 * returns a string
 *
 * @param   string $param1
 * @param   mixed  $param2
 * @return  string
 */
function testWithParams($param1, $param2)
{
    return 'foo';
}
function testWithOutDocBlock()
{
    // intentionally empty
}
/**
 * returns a class
 *
 * @return  org\stubbles\test\lang\reflect\TestWithMethodsAndProperties
 */
function testWithClassReturnType()
{
    return new org\stubbles\test\lang\reflect\TestWithMethodsAndProperties();
}
/**
 * Test for net\stubbles\lang\reflect\ReflectionFunction.
 *
 * @group  lang
 * @group  lang_reflect
 */
class ReflectionFunctionTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to test
     *
     * @type  ReflectionFunction
     */
    protected $refFunction1;
    /**
     * instance 2 to test
     *
     * @type  ReflectionFunction
     */
    protected $refFunction2;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->refFunction1 = new ReflectionFunction('net\\stubbles\\lang\\reflect\\testWithParams');
        $this->refFunction2 = new ReflectionFunction('net\\stubbles\\lang\\reflect\\testWithOutParams');
    }

    /**
     * @test
     */
    public function isEqualToSameInstance()
    {
        $this->assertTrue($this->refFunction1->equals($this->refFunction1));
        $this->assertTrue($this->refFunction2->equals($this->refFunction2));
    }

    /**
     * @test
     */
    public function isEqualToAnotherInstanceForSameFunction()
    {
        $refFunction = new ReflectionFunction('net\\stubbles\\lang\\reflect\\testWithParams');
        $this->assertTrue($this->refFunction1->equals($refFunction));
        $this->assertTrue($refFunction->equals($this->refFunction1));
    }

    /**
     * @test
     */
    public function isNotEqualToAnyOtherInstance()
    {
        $this->assertFalse($this->refFunction1->equals($this->refFunction2));
        $this->assertFalse($this->refFunction2->equals($this->refFunction1));
    }

    /**
     * @test
     */
    public function isNotEqualToAnyOtherType()
    {
        $this->assertFalse($this->refFunction1->equals('foo'));
    }

    /**
     * @test
     */
    public function stringRepresentationContainsNameOfReflectedFunction()
    {
        $this->assertEquals("net\\stubbles\\lang\\reflect\\ReflectionFunction[net\\stubbles\\lang\\reflect\\testWithParams()] {\n}\n",
                            (string) $this->refFunction1
        );
        $this->assertEquals("net\\stubbles\\lang\\reflect\\ReflectionFunction[net\\stubbles\\lang\\reflect\\testWithOutParams()] {\n}\n",
                            (string) $this->refFunction2
        );
    }

    /**
     * @test
     */
    public function hasAnnotationReturnsTrueIfAnnotationIsPresent()
    {
        $this->assertTrue($this->refFunction2->hasAnnotation('FunctionTest'));
    }

    /**
     * @test
     */
    public function hasAnnotationReturnsFalseIfNoAnnotationIsPresent()
    {
        $this->assertFalse($this->refFunction2->hasAnnotation('Other'));
    }

    /**
     * @test
     */
    public function getAnnotationReturnsAnnotation()
    {
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                $this->refFunction2->getAnnotation('FunctionTest')
        );
    }

    /**
     * @test
     */
    public function getParametersReturnsEmptyListIfFunctionDoesNotHaveParameters()
    {
        $this->assertEquals(array(), $this->refFunction2->getParameters());
    }

    /**
     * @test
     */
    public function getParametersReturnsListOfReflectionParameter()
    {
        $refParameters = $this->refFunction1->getParameters();
        $this->assertEquals(2, count($refParameters));
        foreach ($refParameters as $refParameter) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionParameter',
                                    $refParameter
            );
        }
    }

    /**
     * @test
     */
    public function getReturnTypeReturnsNullIfNoDocblockPresent()
    {
        $refFunction3 = new ReflectionFunction('net\\stubbles\\lang\\reflect\\testWithOutDocBlock');
        $this->assertNull($refFunction3->getReturnType());
    }

    /**
     * @test
     */
    public function getReturnTypeReturnsNullIfNoReturnTypePresentInDocblock()
    {
        $this->assertNull($this->refFunction2->getReturnType());
    }

    /**
     * @test
     */
    public function getReturnTypeReturnsReflectionPrimitiveForPrimitiveTypes()
    {

        $this->assertSame(ReflectionPrimitive::$STRING,
                          $this->refFunction1->getReturnType()
        );
    }

    /**
     * @test
     */
    public function getReturnTypeReturnsReflectionClassForObjectTypes()
    {
        $refFunction4 = new ReflectionFunction('net\\stubbles\\lang\\reflect\\testWithClassReturnType');
        $refClass     = $refFunction4->getReturnType();
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('org\\stubbles\\test\\lang\\reflect\\TestWithMethodsAndProperties',
                            $refClass->getName()
        );
    }

    /**
     * @test
     */
    public function getExtensionReturnsNullIfFunctionIsNotPartOfAnExtension()
    {
        $this->assertNull($this->refFunction1->getExtension());
    }

    /**
     * @test
     */
    public function getExtensionReturnsReflectionExtensionIfFunctionIsPartOfAnExtension()
    {
        $refClass = new ReflectionFunction('\date');
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionExtension',
                                $refClass->getExtension()
        );
    }
}
?>