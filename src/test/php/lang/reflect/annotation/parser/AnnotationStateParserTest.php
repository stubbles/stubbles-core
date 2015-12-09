<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect\annotation\parser;
use stubbles\lang\Enum;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * This is a test class that has many annotations.
 *
 * @Foo
 * @FooWithBrackets ()
 * @Bar[TomTom]
 * @MyAnnotation(foo='bar')
 * @TwoParams(foo='bar', test=42)
 * @InvalidChars(foo='ba@r=,')
 * @Constant(foo=stubbles\lang\reflect\annotation\parser\MyTestClass::TEST_CONSTANT)
 * @Enum(foo=stubbles\lang\reflect\annotation\parser\MyTestClass::$FOO)
 * @WithEscaped(foo='This string contains \' and \\, which is possible using escaping...')
 * @Multiline(one=1,
 *            two=2)
 * @Class(stubbles\lang\reflect\annotation\parser\MyTestClass.class)
 */
class MyTestClass extends Enum
{
    const TEST_CONSTANT = 'baz';
    public static $FOO;

    public static function __static()
    {
        self::$FOO = new self('FOO');
    }
}
MyTestClass::__static();
class MyTestClass2
{
    /**
     * a method with an annotation for its parameter
     *
     * @param  string  $bar
     * @ForArgument1{bar}
     * @ForArgument2{bar}(key='value')
     * @MoreArgument1{bar}[Casted]
     * @MoreArgument2{bar}[Casted](key='value')
     * @MoreArgument3[CastedAround]{bar}
     * @MoreArgument4[CastedAround]{bar}(key='value')
     * @another
     */
    public function foo($bar) { }
}
/**
 * Test for stubbles\lang\reflect\annotation\parser\AnnotationStateParser.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  lang_reflect_annotation_parser
 */
class AnnotationStateParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\lang\reflect\annotation\parser\AnnotationStateParser
     */
    private $annotationStateParser;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->annotationStateParser = new AnnotationStateParser();
    }

    /**
     * @param   string  $name
     * @param   array   $values
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     */
    private function createExpectedMyTestClassAnnotation($name, array $values = [], $type = null)
    {
        return [new Annotation($name, MyTestClass::class, $values, $type)];
    }

    /**
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     */
    private function parseMyTestClassAnnotation($type)
    {
        $clazz = new \ReflectionClass(MyTestClass::class);
        return $this->annotationStateParser->parse(
                $clazz->getDocComment(),
                MyTestClass::class
        )[MyTestClass::class]->named($type);
    }

    /**
     * @test
     */
    public function parsesAnnotationWithoutValues()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation('Foo'),
                $this->parseMyTestClassAnnotation('Foo')
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithoutValuesButParentheses()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation('FooWithBrackets'),
                $this->parseMyTestClassAnnotation('FooWithBrackets')
        );
    }

    /**
     * @test
     */
    public function parsesCastedAnnotation()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation('TomTom', [], 'Bar'),
                $this->parseMyTestClassAnnotation('Bar')
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithSingleValue()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation('MyAnnotation', ['foo' => 'bar']),
                $this->parseMyTestClassAnnotation('MyAnnotation')
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithValues()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation(
                        'TwoParams',
                        ['foo' => 'bar', 'test' => 42]
                ),
                $this->parseMyTestClassAnnotation('TwoParams')
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithValueContainingSignalCharacters()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation(
                        'InvalidChars',
                        ['foo' => 'ba@r=,']
                ),
                $this->parseMyTestClassAnnotation('InvalidChars')
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithConstantAsValue()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation(
                        'Constant',
                        ['foo' => MyTestClass::class . '::TEST_CONSTANT']
                ),
                $this->parseMyTestClassAnnotation('Constant')
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithEnumAsValue()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation(
                        'Enum',
                        ['foo' => MyTestClass::class . '::$FOO']
                ),
                $this->parseMyTestClassAnnotation('Enum')
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithStringContainingEscapedCharacters()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation(
                        'WithEscaped',
                        ['foo' => "This string contains ' and \, which is possible using escaping..."]
                ),
                $this->parseMyTestClassAnnotation('WithEscaped')
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationSpanningMultipleLine()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation(
                        'Multiline',
                        ['one' => 1, 'two' => 2]
                ),
                $this->parseMyTestClassAnnotation('Multiline')
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithClassAsValue()
    {
        assertEquals(
                $this->createExpectedMyTestClassAnnotation(
                        'Class',
                        ['__value' => MyTestClass::class . '.class']
                ),
                $this->parseMyTestClassAnnotation('Class')
        );
    }

    /**
     * @test
     */
    public function tabsAreNoProblemForParsing()
    {
        $comment = "/**\n\t * This is a test class that has many annotations.\n\t *\n\t * @Foo\n\t */";
        assertEquals(
                [new Annotation('Foo', 'tabs')],
                $this->annotationStateParser->parse($comment, 'tabs')['tabs']->all()
        );
    }

    /**
     * @param   string  $name
     * @param   array   $values
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     */
    private function createExpectedParameterAnnotation($name, array $values = [], $type = null)
    {
        return [new Annotation($name, MyTestClass2::class . '::foo()#bar', $values, $type)];
    }

    /**
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     */
    private function parseMyTestClass2Annotation($type)
    {
        $method = new \ReflectionMethod(MyTestClass2::class, 'foo');
        return $this->annotationStateParser->parse(
                $method->getDocComment(),
                MyTestClass2::class . '::foo()'
        )[MyTestClass2::class . '::foo()#bar']->named($type);
    }

    /**
     * @test
     */
    public function parsesArgumentAnnotationFromMethodDocComment()
    {
        assertEquals(
                $this->createExpectedParameterAnnotation('ForArgument1'),
                $this->parseMyTestClass2Annotation('ForArgument1')
        );
    }

    /**
     * @test
     */
    public function parsesArgumentAnnotationWithValuesFromMethodDocComment()
    {
        assertEquals(
                $this->createExpectedParameterAnnotation(
                        'ForArgument2',
                        ['key' => 'value']
                ),
                $this->parseMyTestClass2Annotation('ForArgument2')
        );
    }

    /**
     * @test
     */
    public function parsesCastedArgumentAnnotationFromMethodDocComment()
    {
        assertEquals(
                $this->createExpectedParameterAnnotation('Casted', [], 'MoreArgument1'),
                $this->parseMyTestClass2Annotation('MoreArgument1')
        );
    }

    /**
     * @test
     */
    public function parsesCastedArgumentAnnotationWithValuesFromMethodDocComment()
    {
        assertEquals(
                $this->createExpectedParameterAnnotation(
                        'Casted',
                        ['key' => 'value'],
                        'MoreArgument2'
                ),
                $this->parseMyTestClass2Annotation('MoreArgument2')
        );
    }

    /**
     * @test
     */
    public function parsesCastedArgumentAnnotationDifferentOrderFromMethodDocComment()
    {
        assertEquals(
                $this->createExpectedParameterAnnotation('CastedAround', [], 'MoreArgument3'),
                $this->parseMyTestClass2Annotation('MoreArgument3')
        );
    }

    /**
     * @test
     */
    public function parsesCastedArgumentAnnotationDifferentOrderWithValuesFromMethodDocComment()
    {
        assertEquals(
                $this->createExpectedParameterAnnotation(
                        'CastedAround',
                        ['key' => 'value'],
                        'MoreArgument4'
                ),
                $this->parseMyTestClass2Annotation('MoreArgument4')
        );
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function parseIncompleteDocblockThrowsReflectionException()
    {
        $this->annotationStateParser->parse('/**
     * a method with an annotation for its parameter
     *
     * @ForArgument1{bar}',
                'incomplete');
    }



    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function changeStateToUnknownStateThrowsReflectionException()
    {
        $this->annotationStateParser->changeState('invald');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function registerSingleAnnotationAfterParamValueThrowsReflectionException()
    {
        $this->annotationStateParser->registerAnnotation('foo');
        $this->annotationStateParser->registerAnnotationParam('paramName');
        $this->annotationStateParser->setAnnotationParamValue('paramValue');
        $this->annotationStateParser->registerSingleAnnotationParam('singleAnnotationValue');
    }

    /**
     * @test
     * @since  5.5.1
     */
    public function foobar()
    {
        $annotations = $this->annotationStateParser->parse('/**
     * a method with an annotation for its parameter
     *
     * @Foo(name=\'dum "di" dam\')
     */',
                'target');
        assertEquals(
                'dum "di" dam',
                $annotations['target']->firstNamed('Foo')->getName()
        );
    }
}
