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
use stubbles\lang\reflect\annotation\Annotation;
/**
 * a function
 *
 * @param       mixed  $param
 * @FunctionAnnotation
 * @ParamAnno{param}
 * @AnotherAnnotation{param}
 * @Foo{param}('bar')
 * @Foo{param}('baz')
 */
function test_function($param)
{
    // nothing to do
}
function noDocComment($param)
{
    // nothing to do
}
/**
 * missing param information
 */
function misleadingDocComment($param)
{
    // nothing to do
}
/**
 * a class for tests
 */
class ParamTestHelper
{
    /**
     * a method
     *
     * @param  mixed  $param
     * @param  int    $secondParam
     * @MethodAnnotation
     * @ParamAnno{param}
     * @AnotherAnnotation{param}
     * @Foo{param}('bar')
     * @Foo{param}('baz')
     * @SecondParam{secondParam}
     */
    function paramTest($param, $secondParam)
    {
        // nothing to do
    }
}
/**
 * another class for tests
 */
class ParamTestHelper2 extends ParamTestHelper
{
    /**
     * another method
     *
     * @param  ParamTestHelper  $param2
     * @ParamAnno{param2}
     * @SecondParam{secondParam}
     */
    function paramTest2(ParamTestHelper $param2, $secondParam)
    {
        // nothing to do
    }

    /**
     * one more method
     *
     * @param  ParamTestHelper2  $param2
     * @ParamAnno{param}
     */
    function paramTest3(self $param2)
    {
        // nothing to do
    }

    /**
     * one more method
     *
     * @param  array  $param2
     */
    function paramTest4(array $param2)
    {
        // nothing to do
    }
}
/**
 * Test for stubbles\lang\reflect\ReflectionParameter.
 *
 * @group  lang
 * @group  lang_reflect
 */
class ReflectionParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ReflectionParameter
     */
    protected $refParamFunction;
    /**
     * instance to test
     *
     * @type  ReflectionParameter
     */
    protected $refParamMethod1;
    /**
     * instance to test
     *
     * @type  ReflectionParameter
     */
    protected $refParamMethod2;
    /**
     * instance to test
     *
     * @type  ReflectionParameter
     */
    protected $refParamMethod3;
    /**
     * instance to test
     *
     * @type  ReflectionParameter
     */
    protected $refParamMethod4;

    /**
     * create the test environment
     */
    public function setUp()
    {
        $this->refParamFunction = new ReflectionParameter('stubbles\lang\\reflect\test_function', 'param');
        $this->refParamMethod1  = new ReflectionParameter(['stubbles\lang\\reflect\ParamTestHelper',
                                                           'paramTest'
                                                          ],
                                                          'param'
                                      );
        $this->refParamMethod2  = new ReflectionParameter(['stubbles\lang\\reflect\ParamTestHelper2',
                                                           'paramTest'
                                                          ],
                                                          'param'
                                      );
        $this->refParamMethod3  = new ReflectionParameter(['stubbles\lang\\reflect\ParamTestHelper2',
                                                           'paramTest2'
                                                          ],
                                                          'param2'
                                      );
        $this->refParamMethod4  = new ReflectionParameter(['stubbles\lang\\reflect\ParamTestHelper2',
                                                           'paramTest3'
                                                          ],
                                                          'param2'
                                      );
    }

    /**
     * @test
     */
    public function hasAnnotationIfDeclaredInDocblock()
    {
        $this->assertTrue($this->refParamFunction->hasAnnotation('ParamAnno'));
        $this->assertTrue($this->refParamMethod1->hasAnnotation('ParamAnno'));
        $this->assertTrue($this->refParamMethod2->hasAnnotation('ParamAnno'));
        $this->assertTrue($this->refParamMethod3->hasAnnotation('ParamAnno'));
    }

    /**
     * @test
     */
    public function hasNoAnnotationIfNotDeclaredInDocblock()
    {
        $this->assertFalse($this->refParamMethod4->hasAnnotation('ParamAnno'));
    }

    /**
     * @test
     */
    public function retrievedAnnotationsAreOfCorrectType()
    {
        $this->assertInstanceOf('stubbles\lang\\reflect\annotation\Annotation',
                                $this->refParamFunction->getAnnotation('ParamAnno')
        );

        $this->assertInstanceOf('stubbles\lang\\reflect\annotation\Annotation',
                                $this->refParamMethod1->getAnnotation('ParamAnno')
        );

        $this->assertInstanceOf('stubbles\lang\\reflect\annotation\Annotation',
                                $this->refParamMethod2->getAnnotation('ParamAnno')
        );

        $this->assertInstanceOf('stubbles\lang\\reflect\annotation\Annotation',
                                $this->refParamMethod3->getAnnotation('ParamAnno')
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function annotationsReturnsListOfAllAnnotationForFunctionParameter()
    {
        $this->assertEquals(
                [new Annotation('ParamAnno', 'stubbles\lang\reflect\test_function()#param'),
                 new Annotation('AnotherAnnotation', 'stubbles\lang\reflect\test_function()#param'),
                 new Annotation('Foo', 'stubbles\lang\reflect\test_function()#param', ['__value' => 'bar']),
                 new Annotation('Foo', 'stubbles\lang\reflect\test_function()#param', ['__value' => 'baz'])
                ],
                $this->refParamFunction->annotations()->all()
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function annotationsReturnsListOfAllAnnotationForMethodParameter()
    {
        $this->assertEquals(
                [new Annotation('ParamAnno', 'stubbles\lang\reflect\ParamTestHelper::paramTest()#param'),
                 new Annotation('AnotherAnnotation', 'stubbles\lang\reflect\ParamTestHelper::paramTest()#param'),
                 new Annotation('Foo', 'stubbles\lang\reflect\ParamTestHelper::paramTest()#param', ['__value' => 'bar']),
                 new Annotation('Foo', 'stubbles\lang\reflect\ParamTestHelper::paramTest()#param', ['__value' => 'baz'])
                ],
                $this->refParamMethod1->annotations()->all()
        );
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function retrievedNonDeclaredAnnotationThrowsReflectionException()
    {
        $this->refParamMethod4->getAnnotation('ParamAnno');
    }

    /**
     * @test
     */
    public function isEqualToSameInstance()
    {
        $this->assertTrue($this->refParamFunction->equals($this->refParamFunction));
        $this->assertTrue($this->refParamMethod1->equals($this->refParamMethod1));
        $this->assertTrue($this->refParamMethod2->equals($this->refParamMethod2));
        $this->assertTrue($this->refParamMethod3->equals($this->refParamMethod3));
        $this->assertTrue($this->refParamMethod4->equals($this->refParamMethod4));
    }

    /**
     * @test
     */
    public function isEqualToAnotherInstanceForSameParameter()
    {
        $refParamFunction = new ReflectionParameter('stubbles\lang\\reflect\\test_function', 'param');
        $this->assertTrue($this->refParamFunction->equals($refParamFunction));
        $this->assertTrue($refParamFunction->equals($this->refParamFunction));
        $refParamMethod  = new ReflectionParameter(['stubbles\lang\\reflect\ParamTestHelper',
                                                    'paramTest'
                                                   ],
                                                   'param'
                           );
        $this->assertTrue($this->refParamMethod1->equals($refParamMethod));
        $this->assertTrue($refParamMethod->equals($this->refParamMethod1));
    }

    /**
     * @test
     */
    public function isNotEqualToAnotherInstance()
    {
        $this->assertFalse($this->refParamFunction->equals($this->refParamMethod1));
        $this->assertFalse($this->refParamFunction->equals($this->refParamMethod2));
        $this->assertFalse($this->refParamFunction->equals($this->refParamMethod3));
        $this->assertFalse($this->refParamFunction->equals($this->refParamMethod4));
        $this->assertFalse($this->refParamMethod1->equals($this->refParamFunction));
        $this->assertFalse($this->refParamMethod1->equals($this->refParamMethod2));
        $this->assertFalse($this->refParamMethod1->equals($this->refParamMethod3));
        $this->assertFalse($this->refParamMethod1->equals($this->refParamMethod4));
        $this->assertFalse($this->refParamMethod2->equals($this->refParamFunction));
        $this->assertFalse($this->refParamMethod2->equals($this->refParamMethod1));
        $this->assertFalse($this->refParamMethod2->equals($this->refParamMethod3));
        $this->assertFalse($this->refParamMethod2->equals($this->refParamMethod4));
        $this->assertFalse($this->refParamMethod3->equals($this->refParamFunction));
        $this->assertFalse($this->refParamMethod3->equals($this->refParamMethod1));
        $this->assertFalse($this->refParamMethod3->equals($this->refParamMethod2));
        $this->assertFalse($this->refParamMethod3->equals($this->refParamMethod4));
        $this->assertFalse($this->refParamMethod4->equals($this->refParamFunction));
        $this->assertFalse($this->refParamMethod4->equals($this->refParamMethod1));
        $this->assertFalse($this->refParamMethod4->equals($this->refParamMethod2));
        $this->assertFalse($this->refParamMethod4->equals($this->refParamMethod3));
    }

    /**
     * @test
     */
    public function isNotEqualToAnotherType()
    {
        $this->assertFalse($this->refParamFunction->equals('foo'));
    }

    /**
     * test behaviour if casted to string
     *
     * @test
     */
    public function toStringReturnsStringRepresentationWithClassNameOfReflectedMethodAndParameter()
    {
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionParameter[stubbles\\lang\\reflect\\test_function(): Argument param] {\n}\n",
                            (string) $this->refParamFunction
        );
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionParameter[stubbles\\lang\\reflect\\ParamTestHelper::paramTest(): Argument param] {\n}\n",
                            (string) $this->refParamMethod1
        );
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionParameter[stubbles\\lang\\reflect\\ParamTestHelper2::paramTest(): Argument param] {\n}\n",
                            (string) $this->refParamMethod2
        );
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionParameter[stubbles\\lang\\reflect\\ParamTestHelper2::paramTest2(): Argument param2] {\n}\n",
                            (string) $this->refParamMethod3
        );
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionParameter[stubbles\\lang\\reflect\\ParamTestHelper2::paramTest3(): Argument param2] {\n}\n",
                            (string) $this->refParamMethod4
        );
    }

    /**
     * @test
     */
    public function getDeclaringClassReturnsNullForFunctionParameters()
    {
        $this->assertNull($this->refParamFunction->getDeclaringClass());
    }

    /**
     * @test
     */
    public function getDeclaringClassReturnsReflectionClassForClassWhichDeclaresMethod()
    {
        $refClass = $this->refParamMethod1->getDeclaringClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('stubbles\lang\\reflect\ParamTestHelper',
                            $refClass->getName()
        );
        $refClass = $this->refParamMethod2->getDeclaringClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('stubbles\lang\\reflect\ParamTestHelper',
                            $refClass->getName()
        );
        $refClass = $this->refParamMethod3->getDeclaringClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('stubbles\lang\\reflect\ParamTestHelper2',
                            $refClass->getName()
        );
        $refClass = $this->refParamMethod4->getDeclaringClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals('stubbles\lang\\reflect\ParamTestHelper2',
                            $refClass->getName()
        );
    }


    /**
     * @test
     */
    public function getClassReturnsNullIfParameterTypeIsNotClass()
    {
        $this->assertNull($this->refParamFunction->getClass());
        $this->assertNull($this->refParamMethod1->getClass());
        $this->assertNull($this->refParamMethod2->getClass());
    }

    /**
     * @test
     */
    public function getClassReturnsReflectionClassForParameterType()
    {
        $refClass = $this->refParamMethod3->getClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass', $refClass);
        $this->assertEquals('stubbles\lang\\reflect\ParamTestHelper', $refClass->getName());
        $refClass = $this->refParamMethod4->getClass();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass', $refClass);
        $this->assertEquals('stubbles\lang\\reflect\ParamTestHelper2', $refClass->getName());
    }

    /**
     * @since  3.1.1
     * @test
     * @expectedException  \ReflectionException
     */
    public function getTypeThrowsExceptionWhenNoTypeHintAndDocCommentPresent()
    {
        $refParam = new ReflectionParameter('noDocComment', 'param');
        $refParam->getType();
    }

    /**
     * @since  3.1.1
     * @test
     * @expectedException  \ReflectionException
     */
    public function getTypeThrowsExceptionWhenDocCommentDoesNotContainParamInfo()
    {
        $refParam = new ReflectionParameter('misleadingDocComment', 'param');
        $refParam->getType();
    }

    /**
     * @since  3.1.1
     * @test
     */
    public function getTypeReturnsTypeFromClassTypeHint()
    {
        $refClass = $this->refParamMethod3->getType();
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass', $refClass);
        $this->assertEquals('stubbles\lang\\reflect\ParamTestHelper',
                            $refClass->getName()
        );
    }

    /**
     * @since  3.1.1
     * @test
     */
    public function getTypeReturnsTypeFromArrayTypeHint()
    {
        $refParam = new ReflectionParameter(['stubbles\lang\\reflect\ParamTestHelper2',
                                             'paramTest4'
                                            ],
                                            'param2'
                    );
        $this->assertSame(ReflectionPrimitive::$ARRAY, $refParam->getType());
    }

    /**
     * @since  3.1.1
     * @test
     */
    public function getTypeReturnsTypeFromDocCommentForPrimitives()
    {
        $refParam = new ReflectionParameter(['stubbles\lang\\reflect\ParamTestHelper',
                                             'paramTest'
                                            ],
                                            'secondParam'
                    );
        $this->assertSame(ReflectionPrimitive::$INT, $refParam->getType());
    }

    /**
     * @since  3.1.1
     * @test
     */
    public function getTypeReturnsTypeFromDocCommentForMixed()
    {
        $refParam = new ReflectionParameter(['stubbles\lang\\reflect\ParamTestHelper',
                                             'paramTest'
                                            ],
                                            'param'
                    );
        $this->assertSame(MixedType::$MIXED, $refParam->getType());
    }
}
