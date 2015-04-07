<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect;
/**
 * Tests for stubbles\lang\reflect\*().
 *
 * @since  5.3.0
 * @group  lang
 * @group  lang_reflect
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function annotationsWithMethodNameReturnsMethodAnnotations()
    {
        assertEquals(
                __CLASS__ . '::' . __FUNCTION__ . '()',
                annotationsOf(__CLASS__, __FUNCTION__)->target()
        );
    }

    /**
     * @test
     */
    public function annotationsWithClassNameReturnsClassAnnotations()
    {
        assertEquals(
                __CLASS__,
                annotationsOf(__CLASS__)->target()
        );
    }

    /**
     * @test
     */
    public function constructorAnnotationsWithClassNameReturnsConstructorAnnotations()
    {
        assertEquals(
                'PHPUnit_Framework_TestCase::__construct()',
                annotationsOfConstructor(__CLASS__)->target()
        );
    }

    /**
     * @test
     */
    public function annotationsWithClassInstanceReturnsClassAnnotations()
    {
        assertEquals(
                __CLASS__,
                annotationsOf($this)->target()
        );
    }

    /**
     * @test
     */
    public function constructorAnnotationsWithClassInstanceReturnsConstructorAnnotations()
    {
        assertEquals(
                'PHPUnit_Framework_TestCase::__construct()',
                annotationsOfConstructor($this)->target()
        );
    }

    /**
     * @test
     */
    public function annotationsWithFunctionNameReturnsFunctionAnnotations()
    {
        assertEquals(
                'stubbles\lang\reflect\annotationsOf()',
                annotationsOf('stubbles\lang\reflect\annotationsOf')->target()
        );
    }

    /**
     * @test
     * @expectedException  ReflectionException
     */
    public function annotationsWithUnknownClassAndFunctionNameThrowsReflectionException()
    {
        annotationsOf('doesNotExist');
    }

    /**
     * @param  string  $refParam
     */
    private function example($refParam)
    {

    }

    /**
     * @test
     */
    public function annotationsWithReflectionParameterReturnsParameterAnnotations()
    {
        $refParam = new \ReflectionParameter([$this, 'example'], 'refParam');
        assertEquals(
                __CLASS__ . '::example()#refParam',
                annotationsOf($refParam)->target()
        );
    }

    /**
     * @test
     */
    public function annotationsOfParameterWithClassInstanceReturnsParameterAnnotations()
    {
        assertEquals(
                __CLASS__ . '::example()#refParam',
                annotationsOfParameter('refParam', $this, 'example')->target()
        );
    }

    /**
     * @test
     */
    public function annotationsOfParameterWithClassNameReturnsParameterAnnotations()
    {
        assertEquals(
                __CLASS__ . '::example()#refParam',
                annotationsOfParameter('refParam', __CLASS__, 'example')->target()
        );
    }

    /**
     * @type  null
     */
    private $someProperty;
    /**
     *
     * @type  null
     */
    private static $otherProperty;

    /**
     * @return  array
     */
    public function properties()
    {
        return [['->', 'someProperty'], ['::$', 'otherProperty']];
    }

    /**
     * @test
     * @dataProvider  properties
     */
    public function annotationsWithReflectionPropertyReturnsPropertyAnnotations($connector, $propertyName)
    {
        $refProperty = new \ReflectionProperty($this, $propertyName);
        assertEquals(
                __CLASS__ . $connector . $propertyName,
                annotationsOf($refProperty)->target()
        );
    }

    /**
     * @test
     * @expectedException  ReflectionException
     */
    public function annotationTargetThrowsReflectionExceptionForNonSupportedAnnotationPlaces()
    {
        _annotationTarget(new \ReflectionExtension('date'));
    }

    /**
     * @test
     * @expectedException  ReflectionException
     */
    public function docCommentThrowsReflectionExceptionForNonSupportedAnnotationPlaces()
    {
        docComment(new \ReflectionExtension('date'));
    }

    /**
     * @test
     */
    public function methodsOfReturnsAllMethods()
    {
        assertGreaterThan(
                0,
                methodsOf($this)
                    ->peek(
                        function($method)
                        {
                            assertInstanceOf('\ReflectionMethod', $method);
                        },
                        function($methodName)
                        {
                            assertTrue(method_exists($this, $methodName));
                        }
                    )->count()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function methodsWithNonClassThrowsInvalidArgumentException()
    {
        methodsOf(404);
    }

    /**
     * @test
     */
    public function propertiesOfReturnsAllMethods()
    {
        assertGreaterThan(
                0,
                propertiesOf($this)
                    ->peek(
                        function($property)
                        {
                            assertInstanceOf('\ReflectionProperty', $property);
                        },
                        function($propertyName)
                        {
                            assertTrue(property_exists($this, $propertyName));
                        }
                    )->count()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function propertiesOfWithNonClassThrowsInvalidArgumentException()
    {
        propertiesOf(404);
    }

    /**
     * @test
     */
    public function parametersOfReturnsAllParameters()
    {
        assertEquals(
                1,
                parametersOf($this, 'example')
                    ->peek(
                        function($parameter)
                        {
                            assertInstanceOf('\ReflectionParameter', $parameter);
                        },
                        function($paramName)
                        {
                            assertEquals('refParam', $paramName);
                        }
                    )->count()
        );
    }

    /**
     * @test
     */
    public function parametersOfWithReflectionMethodReturnsAllParameters()
    {
        assertEquals(
                1,
                parametersOf(new \ReflectionMethod($this, 'example'))
                    ->peek(
                        function($parameter)
                        {
                            assertInstanceOf('\ReflectionParameter', $parameter);
                        },
                        function($paramName)
                        {
                            assertEquals('refParam', $paramName);
                        }
                    )->count()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function parametersOfWithNonParametersReferenceThrowsInvalidArgumentException()
    {
        parametersOf(404);
    }

    /**
     * @test
     */
    public function parameterReturnsExactReflectionParameter()
    {
        assertEquals(
                'refParam',
                parameter('refParam', $this, 'example')->getName()
        );
    }
}
