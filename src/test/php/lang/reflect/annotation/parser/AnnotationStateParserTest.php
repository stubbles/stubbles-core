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
use stubbles\lang\reflect\annotation\Annotation;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
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
 * @WithEscaped(foo='This string contains \' and \\, which is possible using escaping...')
 * @Multiline(one=1,
 *            two=2)
 * @Class(stubbles\lang\reflect\annotation\parser\MyTestClass.class)
 */
class MyTestClass
{
    const TEST_CONSTANT = 'baz';
}
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
    private function expectedClassAnnotation($name, array $values = [], $type = null)
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
        assert(
                $this->parseMyTestClassAnnotation('Foo'),
                equals($this->expectedClassAnnotation('Foo'))
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithoutValuesButParentheses()
    {
        assert(
                $this->parseMyTestClassAnnotation('FooWithBrackets'),
                equals($this->expectedClassAnnotation('FooWithBrackets'))
        );
    }

    /**
     * @test
     */
    public function parsesCastedAnnotation()
    {
        assert(
                $this->parseMyTestClassAnnotation('Bar'),
                equals($this->expectedClassAnnotation('TomTom', [], 'Bar'))
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithSingleValue()
    {
        assert(
                $this->parseMyTestClassAnnotation('MyAnnotation'),
                equals($this->expectedClassAnnotation('MyAnnotation', ['foo' => 'bar']))
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithValues()
    {
        assert(
                $this->parseMyTestClassAnnotation('TwoParams'),
                equals($this->expectedClassAnnotation(
                        'TwoParams',
                        ['foo' => 'bar', 'test' => 42]
                ))
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithValueContainingSignalCharacters()
    {
        assert(
                $this->parseMyTestClassAnnotation('InvalidChars'),
                equals($this->expectedClassAnnotation(
                        'InvalidChars',
                        ['foo' => 'ba@r=,']
                ))
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithConstantAsValue()
    {
        assert(
                $this->parseMyTestClassAnnotation('Constant'),
                equals($this->expectedClassAnnotation(
                        'Constant',
                        ['foo' => MyTestClass::class . '::TEST_CONSTANT']
                ))
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithStringContainingEscapedCharacters()
    {
        assert(
                $this->parseMyTestClassAnnotation('WithEscaped'),
                equals($this->expectedClassAnnotation(
                        'WithEscaped',
                        ['foo' => "This string contains ' and \, which is possible using escaping..."]
                ))
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationSpanningMultipleLine()
    {
        assert(
                $this->parseMyTestClassAnnotation('Multiline'),
                equals($this->expectedClassAnnotation(
                        'Multiline',
                        ['one' => 1, 'two' => 2]
                ))
        );
    }

    /**
     * @test
     */
    public function parsesAnnotationWithClassAsValue()
    {
        assert(
                $this->parseMyTestClassAnnotation('Class'),
                equals($this->expectedClassAnnotation(
                        'Class',
                        ['__value' => MyTestClass::class . '.class']
                ))
        );
    }

    /**
     * @test
     */
    public function tabsAreNoProblemForParsing()
    {
        $comment = "/**\n\t * This is a test class that has many annotations.\n\t *\n\t * @Foo\n\t */";
        assert(
                $this->annotationStateParser->parse($comment, 'tabs')['tabs']->all(),
                equals([new Annotation('Foo', 'tabs')])
        );
    }

    /**
     * @param   string  $name
     * @param   array   $values
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     */
    private function expectedParameterAnnotation($name, array $values = [], $type = null)
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
        assert(
                $this->parseMyTestClass2Annotation('ForArgument1'),
                equals($this->expectedParameterAnnotation('ForArgument1'))
        );
    }

    /**
     * @test
     */
    public function parsesArgumentAnnotationWithValuesFromMethodDocComment()
    {
        assert(
                $this->parseMyTestClass2Annotation('ForArgument2'),
                equals($this->expectedParameterAnnotation(
                        'ForArgument2',
                        ['key' => 'value']
                ))
        );
    }

    /**
     * @test
     */
    public function parsesCastedArgumentAnnotationFromMethodDocComment()
    {
        assert(
                $this->parseMyTestClass2Annotation('MoreArgument1'),
                equals($this->expectedParameterAnnotation(
                        'Casted',
                        [],
                        'MoreArgument1'
                ))
        );
    }

    /**
     * @test
     */
    public function parsesCastedArgumentAnnotationWithValuesFromMethodDocComment()
    {
        assert(
                $this->parseMyTestClass2Annotation('MoreArgument2'),
                equals($this->expectedParameterAnnotation(
                        'Casted',
                        ['key' => 'value'],
                        'MoreArgument2'
                ))
        );
    }

    /**
     * @test
     */
    public function parsesCastedArgumentAnnotationDifferentOrderFromMethodDocComment()
    {
        assert(
                $this->parseMyTestClass2Annotation('MoreArgument3'),
                equals($this->expectedParameterAnnotation(
                        'CastedAround',
                        [],
                        'MoreArgument3'
                ))
        );
    }

    /**
     * @test
     */
    public function parsesCastedArgumentAnnotationDifferentOrderWithValuesFromMethodDocComment()
    {
        assert(
                $this->parseMyTestClass2Annotation('MoreArgument4'),
                equals($this->expectedParameterAnnotation(
                        'CastedAround',
                        ['key' => 'value'],
                        'MoreArgument4'
                ))
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
        assert(
                $annotations['target']->firstNamed('Foo')->getName(),
                equals('dum "di" dam')
        );
    }
}
