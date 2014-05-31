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
/**
 * This is a test class that has many annotations.
 *
 * @Foo
 * @FooWithBrackets ()
 * @Bar[TomTom]
 * @Test
 * @MyAnnotation(foo='bar')
 * @TwoParams(foo='bar', test=42)
 * @InvalidChars(foo='ba@r=,')
 * @SingleValue(42)
 * @Constant(foo=stubbles\lang\reflect\annotation\parser\MyTestClass::TEST_CONSTANT)
 * @Enum(foo=stubbles\lang\reflect\annotation\parser\MyTestClass::$FOO)
 * @SingleStringValue('This is a string with chars like = or ,')
 * @WithEscaped(foo='This string contains \' and \\, which is possible using escaping...')
 * @Multiline(one=1,
 *            two=2)
 * @WithTypes(true=true, false=false, integer=4562,
 *            null=null,
 *            negInt=-13,
 *            double=2.34,
 *            negDouble=-5.67,
 *            string1='true',
 *            string2='null',
 *            class=stubbles\lang\reflect\annotation\parser\MyTestClass.class)
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
     */
    public function foo($bar) { }
}
/**
 * Test class for bug 202.
 */
class Bug202Class
{
    /**
     * a method with an annotation for its parameter
     *
     * @ForArgument1{bar}
     * @ForArgument2{bar}(key='value')
     * @MoreArgument1{bar}[Casted]
     * @MoreArgument2{bar}[Casted](key='value')
     * @MoreArgument3[CastedAround]{bar}
     * @MoreArgument4[CastedAround]{bar}(key='value')
     * @param   string  $bar
     * @return  string  (this should be 'baz')
     */
    public function foo($bar) { return 'baz'; }
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
     * test that checking if an annotation is present works as expected
     *
     * @test
     */
    public function parse()
    {
        $clazz                 = new \ReflectionClass('stubbles\lang\reflect\annotation\parser\MyTestClass');
        $annotationStateParser = new AnnotationStateParser();
        $annotations           = $annotationStateParser->parse($clazz->getDocComment());
        $this->assertTrue(isset($annotations['Foo']));
        $this->assertEquals('Foo', $annotations['Foo']['type']);
        $this->assertEquals([], $annotations['Foo']['params']);
        $this->assertTrue(isset($annotations['FooWithBrackets']));
        $this->assertEquals('FooWithBrackets', $annotations['FooWithBrackets']['type']);
        $this->assertEquals([], $annotations['FooWithBrackets']['params']);
        $this->assertTrue(isset($annotations['Bar']));
        $this->assertEquals('TomTom', $annotations['Bar']['type']);
        $this->assertEquals([], $annotations['Bar']['params']);
        $this->assertTrue(isset($annotations['Test']));
        $this->assertEquals('Test', $annotations['Test']['type']);
        $this->assertEquals([], $annotations['Test']['params']);
        $this->assertTrue(isset($annotations['MyAnnotation']));
        $this->assertEquals('MyAnnotation', $annotations['MyAnnotation']['type']);
        $this->assertEquals(['foo' => 'bar'], $annotations['MyAnnotation']['params']);
        $this->assertTrue(isset($annotations['TwoParams']));
        $this->assertEquals('TwoParams', $annotations['TwoParams']['type']);
        $this->assertEquals(['foo' => 'bar', 'test' => 42], $annotations['TwoParams']['params']);
        $this->assertTrue(isset($annotations['InvalidChars']));
        $this->assertEquals('InvalidChars', $annotations['InvalidChars']['type']);
        $this->assertEquals(['foo' => 'ba@r=,'], $annotations['InvalidChars']['params']);
        $this->assertTrue(isset($annotations['SingleValue']));
        $this->assertEquals('SingleValue', $annotations['SingleValue']['type']);
        $this->assertEquals(['__value' => 42], $annotations['SingleValue']['params']);
        $this->assertTrue(isset($annotations['Constant']));
        $this->assertEquals('Constant', $annotations['Constant']['type']);
        $this->assertEquals(['foo' => MyTestClass::TEST_CONSTANT], $annotations['Constant']['params']);
        $this->assertTrue(isset($annotations['Enum']));
        $this->assertEquals('Enum', $annotations['Enum']['type']);
        $this->assertEquals(['foo' => MyTestClass::$FOO], $annotations['Enum']['params']);
        $this->assertTrue(isset($annotations['SingleStringValue']));
        $this->assertEquals('SingleStringValue', $annotations['SingleStringValue']['type']);
        $this->assertEquals(['__value' => 'This is a string with chars like = or ,'], $annotations['SingleStringValue']['params']);
        $this->assertTrue(isset($annotations['WithEscaped']));
        $this->assertEquals('WithEscaped', $annotations['WithEscaped']['type']);
        $this->assertEquals(['foo' => "This string contains ' and \, which is possible using escaping..."], $annotations['WithEscaped']['params']);
        $this->assertTrue(isset($annotations['Multiline']));
        $this->assertEquals('Multiline', $annotations['Multiline']['type']);
        $this->assertEquals(['one' => 1, 'two' => 2], $annotations['Multiline']['params']);
        $this->assertTrue(isset($annotations['WithTypes']));
        $this->assertEquals('WithTypes', $annotations['WithTypes']['type']);
        $this->assertTrue($annotations['WithTypes']['params']['true']);
        $this->assertFalse($annotations['WithTypes']['params']['false']);
        $this->assertEquals(4562, $annotations['WithTypes']['params']['integer']);
        $this->assertEquals('integer', gettype($annotations['WithTypes']['params']['integer']));
        $this->assertNull($annotations['WithTypes']['params']['null']);
        $this->assertEquals(-13, $annotations['WithTypes']['params']['negInt']);
        $this->assertEquals('integer', gettype($annotations['WithTypes']['params']['negInt']));
        $this->assertEquals(2.34, $annotations['WithTypes']['params']['double']);
        $this->assertEquals('double', gettype($annotations['WithTypes']['params']['double']));
        $this->assertEquals(-5.67, $annotations['WithTypes']['params']['negDouble']);
        $this->assertEquals('double', gettype($annotations['WithTypes']['params']['negDouble']));
        $this->assertEquals('true', $annotations['WithTypes']['params']['string1']);
        $this->assertEquals('null', $annotations['WithTypes']['params']['string2']);
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass', $annotations['WithTypes']['params']['class']);
        $this->assertEquals('stubbles\lang\\reflect\annotation\parser\MyTestClass', $annotations['WithTypes']['params']['class']->getName());
    }

    /**
     * test that tabs are recognized correctly
     *
     * @test
     */
    public function tabs()
    {
        $comment = "/**\n\t * This is a test class that has many annotations.\n\t *\n\t * @Foo\n\t */";
        $annotationStateParser = new AnnotationStateParser();
        $annotations           = $annotationStateParser->parse($comment);
        $this->assertTrue(isset($annotations['Foo']));
    }

    /**
     * test that parameter argumentations are recognized correctly
     *
     * @test
     */
    public function argument()
    {
        $method                = new \ReflectionMethod('stubbles\lang\\reflect\annotation\parser\MyTestClass2', 'foo');
        $annotationStateParser = new AnnotationStateParser();
        $annotations           = $annotationStateParser->parse($method->getDocComment());
        $this->assertTrue(isset($annotations['ForArgument1#bar']));
        $this->assertEquals('bar', $annotations['ForArgument1#bar']['argument']);
        $this->assertEquals('ForArgument1', $annotations['ForArgument1#bar']['type']);
        $this->assertEquals([], $annotations['ForArgument1#bar']['params']);
        $this->assertTrue(isset($annotations['ForArgument2#bar']));
        $this->assertEquals('bar', $annotations['ForArgument2#bar']['argument']);
        $this->assertEquals('ForArgument2', $annotations['ForArgument2#bar']['type']);
        $this->assertEquals(['key' => 'value'], $annotations['ForArgument2#bar']['params']);
        $this->assertTrue(isset($annotations['MoreArgument1#bar']));
        $this->assertEquals('bar', $annotations['MoreArgument1#bar']['argument']);
        $this->assertEquals('Casted', $annotations['MoreArgument1#bar']['type']);
        $this->assertEquals([], $annotations['MoreArgument1#bar']['params']);
        $this->assertTrue(isset($annotations['MoreArgument2#bar']));
        $this->assertEquals('bar', $annotations['MoreArgument2#bar']['argument']);
        $this->assertEquals('Casted', $annotations['MoreArgument2#bar']['type']);
        $this->assertEquals(['key' => 'value'], $annotations['MoreArgument2#bar']['params']);
        $this->assertTrue(isset($annotations['MoreArgument3#bar']));
        $this->assertEquals('bar', $annotations['MoreArgument3#bar']['argument']);
        $this->assertEquals('CastedAround', $annotations['MoreArgument3#bar']['type']);
        $this->assertEquals([], $annotations['MoreArgument3#bar']['params']);
        $this->assertTrue(isset($annotations['MoreArgument4#bar']));
        $this->assertEquals('bar', $annotations['MoreArgument4#bar']['argument']);
        $this->assertEquals('CastedAround', $annotations['MoreArgument4#bar']['type']);
        $this->assertEquals(['key' => 'value'], $annotations['MoreArgument4#bar']['params']);
    }

    /**
     * test that parameter argumentations are recognized correctly
     *
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function bug202()
    {
        $method                = new \ReflectionMethod('stubbles\lang\\reflect\annotation\parser\Bug202Class', 'foo');
        $annotationStateParser = new AnnotationStateParser();
        $annotations           = $annotationStateParser->parse($method->getDocComment());
        $this->assertTrue(isset($annotations['ForArgument1#bar']));
        $this->assertEquals('bar', $annotations['ForArgument1#bar']['argument']);
        $this->assertEquals('ForArgument1', $annotations['ForArgument1#bar']['type']);
        $this->assertEquals([], $annotations['ForArgument1#bar']['params']);
        $this->assertTrue(isset($annotations['ForArgument2#bar']));
        $this->assertEquals('bar', $annotations['ForArgument2#bar']['argument']);
        $this->assertEquals('ForArgument2', $annotations['ForArgument2#bar']['type']);
        $this->assertEquals(['key' => 'value'], $annotations['ForArgument2#bar']['params']);
        $this->assertTrue(isset($annotations['MoreArgument1#bar']));
        $this->assertEquals('bar', $annotations['MoreArgument1#bar']['argument']);
        $this->assertEquals('Casted', $annotations['MoreArgument1#bar']['type']);
        $this->assertEquals([], $annotations['MoreArgument1#bar']['params']);
        $this->assertTrue(isset($annotations['MoreArgument2#bar']));
        $this->assertEquals('bar', $annotations['MoreArgument2#bar']['argument']);
        $this->assertEquals('Casted', $annotations['MoreArgument2#bar']['type']);
        $this->assertEquals(['key' => 'value'], $annotations['MoreArgument2#bar']['params']);
        $this->assertTrue(isset($annotations['MoreArgument3#bar']));
        $this->assertEquals('bar', $annotations['MoreArgument3#bar']['argument']);
        $this->assertEquals('CastedAround', $annotations['MoreArgument3#bar']['type']);
        $this->assertEquals([], $annotations['MoreArgument3#bar']['params']);
        $this->assertTrue(isset($annotations['MoreArgument4#bar']));
        $this->assertEquals('bar', $annotations['MoreArgument4#bar']['argument']);
        $this->assertEquals('CastedAround', $annotations['MoreArgument4#bar']['type']);
        $this->assertEquals(['key' => 'value'], $annotations['MoreArgument4#bar']['params']);
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function parseIncompleteDocblockThrowsReflectionException()
    {
        $annotationStateParser = new AnnotationStateParser();
        $annotationStateParser->parse('/**
     * a method with an annotation for its parameter
     *
     * @ForArgument1{bar}');
    }



    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function changeStateToUnknownStateThrowsReflectionException()
    {
        $annotationStateParser = new AnnotationStateParser();
        $annotationStateParser->changeState('invald');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function registerSingleAnnotationAfterParamValueThrowsReflectionException()
    {
        $annotationStateParser = new AnnotationStateParser();
        $annotationStateParser->registerAnnotationParam('paramName');
        $annotationStateParser->setAnnotationParamValue('paramValue');
        $annotationStateParser->registerSingleAnnotationParam('singleAnnotationValue');
    }
}
