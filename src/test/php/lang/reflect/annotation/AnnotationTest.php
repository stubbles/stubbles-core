<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect\annotation;
use stubbles\lang\Enum;
/**
 * Helper class for the test.
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
/**
 * Test for stubbles\lang\reflect\annotation\Annotation.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  bug252
 */
class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param   array $values
     * @return  \stubbles\lang\reflect\annotation\Annotation
     */
    private function createAnnotation(array $values = [])
    {
        return new Annotation('Life', 'someFunction()', $values, 'Example');
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function returnsGivenTargetName()
    {
        assertEquals(
                'someFunction()',
                $this->createAnnotation()->target()
        );
    }

    /**
     * @test
     * @expectedException  ReflectionException
     * @expectedExceptionMessage  The value with name "invalid" for annotation @Example[Life] at someFunction() does not exist
     */
    public function callUndefinedMethodThrowsReflectionException()
    {
        $this->createAnnotation()->invalid();
    }

    /**
     * @param   string  $value
     * @return  \stubbles\lang\reflect\annotation\Annotation
     */
    private function createSingleValueAnnotation($value)
    {
        return $this->createAnnotation(['__value' => $value]);
    }

    /**
     * @test
     */
    public function returnsSpecialValueForAllMethodCallsWithGet()
    {
        $annotation = $this->createSingleValueAnnotation('bar');
        assertEquals('bar',
                            $annotation->getFoo()
        );
        assertEquals('bar',
                            $annotation->getOther()
        );
    }

    /**
     * @test
     */
    public function returnsSpecialValueForAllMethodCallsWithIs()
    {
        $annotation = $this->createSingleValueAnnotation('true');
        assertTrue($annotation->isFoo());
        assertTrue($annotation->isOther());
    }

    /**
     * @test
     * @expectedException  ReflectionException
     * @expectedExceptionMessage  The value with name "invalid" for annotation @Example at someFunction() does not exist
     */
    public function throwsReflectionExceptionForMethodCallsWithoutGetOrIsOnSpecialValue()
    {
        $annotation = new Annotation('Example', 'someFunction()', ['__value' => 'true']);
        $annotation->invalid();
    }

    /**
     * @test
     * @group  value_by_name
     * @since  1.7.0
     */
    public function returnsFalseOnCheckForUnsetProperty()
    {
        assertFalse($this->createAnnotation()->hasValueByName('foo'));
    }

    /**
     * @test
     * @group  value_by_name
     * @since  1.7.0
     */
    public function returnsTrueOnCheckForSetProperty()
    {
        assertTrue(
                $this->createAnnotation(['foo' => 'hello'])->hasValueByName('foo')
        );
    }

    /**
     * @test
     * @group  value_by_name
     * @since  1.7.0
     */
    public function returnsNullForUnsetProperty()
    {
        assertNull($this->createAnnotation()->getValueByName('foo'));
    }

    /**
     * @test
     * @group  value_by_name
     * @since  5.0.0
     */
    public function returnsDefaultForUnsetProperty()
    {
        assertEquals(
                'bar',
                $this->createAnnotation()->getValueByName('foo', 'bar')
        );
    }

    /**
     * @test
     * @group  value_by_name
     * @since  1.7.0
     */
    public function returnsValueForSetProperty()
    {
        assertEquals(
                'hello',
                $this->createAnnotation(['foo' => 'hello'])->getValueByName('foo')
        );
    }

    /**
     * @test
     */
    public function returnsNullForUnsetGetProperty()
    {
        assertNull($this->createAnnotation()->getFoo());
    }

    /**
     * @test
     */
    public function returnsFalseForUnsetBooleanProperty()
    {
        assertFalse($this->createAnnotation()->isFoo());
    }

    /**
     * @test
     */
    public function returnsValueOfGetProperty()
    {
        assertEquals(
                'bar',
                $this->createAnnotation(['foo' => 'bar'])->getFoo()
        );
    }

    /**
     * @test
     */
    public function returnsFirstArgumentIfGetPropertyNotSet()
    {
        assertEquals(
                'bar',
                $this->createAnnotation()->getFoo('bar')
        );
    }

    /**
     * @return  array
     */
    public function booleanValues()
    {
        return [['true'], ['yes'], ['on']];
    }

    /**
     * @test
     * @dataProvider  booleanValues
     */
    public function returnsValueOfBooleanProperty($bool)
    {
        assertTrue($this->createAnnotation(['foo' => $bool])->isFoo());
    }

    /**
     * @test
     */
    public function returnTrueForValueCheckIfValueSet()
    {
        assertTrue($this->createSingleValueAnnotation('bar')->hasValue());
    }

    /**
     * @test
     */
    public function returnFalseForValueCheckIfValueNotSet()
    {
        assertFalse($this->createAnnotation()->hasValue());
    }

    /**
     * @test
     */
    public function returnFalseForValueCheckIfAnotherPropertySet()
    {
        assertFalse($this->createAnnotation(['foo' => 'bar'])->hasValue());
    }

    /**
     * @test
     */
    public function returnTrueForPropertyCheckIfPropertySet()
    {
        $annotation = $this->createAnnotation(['foo' => 'bar', 'baz' => 'true']);
        assertTrue($annotation->hasFoo());
        assertTrue($annotation->hasBaz());
    }

    /**
     * @test
     */
    public function returnFalseForPropertyCheckIfPropertyNotSet()
    {
        assertFalse($this->createAnnotation()->hasFoo());
        assertFalse($this->createAnnotation()->hasBaz());
    }

    /**
     * @test
     */
    public function canAccessPropertyAsMethod()
    {
        assertEquals(
                'bar',
                $this->createAnnotation(['foo' => 'bar'])->foo()
        );
    }

    /**
     * @test
     */
    public function canAccessBooleanPropertyAsMethod()
    {
        assertTrue($this->createAnnotation(['foo' => 'true'])->foo());
    }

    /**
     * @return  array
     */
    public function valueTypes()
    {
        return [
            [true, 'true'],
            [false, 'false'],
            [null, 'null'],
            [4562, '4562'],
            [-13, '-13'],
            [2.34, '2.34'],
            [-5.67, '-5.67'],
            [new \ReflectionClass(__CLASS__), __CLASS__ . '.class'],
            ['true', "'true'"],
            ['null', '"null"'],
            [MyTestClass::TEST_CONSTANT, 'stubbles\lang\reflect\annotation\MyTestClass::TEST_CONSTANT'],
            [MyTestClass::$FOO, 'stubbles\lang\reflect\annotation\MyTestClass::$FOO']
        ];
    }

    /**
     *
     * @param type $expected
     * @param type $stringValue
     * @test
     * @dataProvider  valueTypes
     * @since  4.1.0
     */
    public function parsesValuesToTypes($expected, $stringValue)
    {
        assertEquals(
                $expected,
                $this->createAnnotation(['foo' => $stringValue])->foo()
        );
    }

    /**
     *
     * @param type $expected
     * @param type $stringValue
     * @test
     * @dataProvider  valueTypes
     * @since  4.1.0
     */
    public function parsesValuesToTypesWithGet($expected, $stringValue)
    {
        assertEquals(
                $expected,
                $this->createAnnotation(['foo' => $stringValue])->getFoo()
        );
    }

    /**
     *
     * @param type $expected
     * @param type $stringValue
     * @test
     * @dataProvider  valueTypes
     * @since  4.1.0
     */
    public function parsesValuesToTypesWithGetValueByName($expected, $stringValue)
    {
        assertEquals(
                $expected,
                $this->createAnnotation(['foo' => $stringValue])->getValueByName('foo')
        );
    }

    /**
     *
     * @param type $expected
     * @param type $stringValue
     * @test
     * @dataProvider  valueTypes
     * @since  4.1.0
     */
    public function parsesValuesToTypesWithSingleValue($expected, $stringValue)
    {
        assertEquals(
                $expected,
                $this->createSingleValueAnnotation($stringValue)->getValue()
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function canBeCastedToString()
    {
        assertInternalType(
                'string',
                (string) $this->createAnnotation(['foo' => 'value'])
        );
    }

    /**
     * @return  array
     * @since  5.0.0
     */
    public function parseList()
    {
        return [
            ['This is a string', 'This is a string', 'asString'],
            [303, '303', 'asInt'],
            [3.13, '3.13', 'asFloat'],
            [false, '1', 'asBool'],
            [true, 'true', 'asBool'],
            [false, 'false', 'asBool'],
            [['foo', 'bar', 'baz'], '[foo|bar|baz]', 'asList'],
            [['foo' => 'bar', 'baz'], 'foo:bar|baz', 'asMap'],
            [[1, 2, 3, 4, 5], '1..5', 'asRange']
        ];
    }

    /**
     * @param  mixed   $expected
     * @param  string  $value
     * @param  string  $type
     * @test
     * @dataProvider  parseList
     * @since  5.0.0
     */
    public function parseReturnsValueCastedToRecognizedType($expected, $value, $type)
    {
        assertEquals($expected, $this->createAnnotation(['foo' => $value])->parse('foo')->$type());
    }
}
