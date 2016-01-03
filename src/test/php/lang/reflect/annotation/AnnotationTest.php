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
use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Helper class for the test.
 */
class MyTestClass
{
    const TEST_CONSTANT = 'baz';
}
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
        assert($this->createAnnotation()->target(), equals('someFunction()'));
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
        assert($annotation->getFoo(), equals('bar'));
        assert($annotation->getOther(), equals('bar'));
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
                $this->createAnnotation(['foo' => 'hello'])
                        ->hasValueByName('foo')
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
        assert(
                $this->createAnnotation()->getValueByName('foo', 'bar'),
                equals('bar')
        );
    }

    /**
     * @test
     * @group  value_by_name
     * @since  1.7.0
     */
    public function returnsValueForSetProperty()
    {
        assert(
                $this->createAnnotation(['foo' => 'hello'])->getValueByName('foo'),
                equals('hello')
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
        assert(
                $this->createAnnotation(['foo' => 'bar'])->getFoo(),
                equals('bar')
        );
    }

    /**
     * @test
     */
    public function returnsFirstArgumentIfGetPropertyNotSet()
    {
        assert(
                $this->createAnnotation()->getFoo('bar'),
                equals('bar')
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
        $annotation = $this->createAnnotation(['foo' => 'bar']);
        assertTrue($annotation->hasFoo());
    }

    /**
     * @test
     */
    public function returnFalseForPropertyCheckIfPropertyNotSet()
    {
        assertFalse($this->createAnnotation()->hasFoo());
    }

    /**
     * @test
     */
    public function canAccessPropertyAsMethod()
    {
        assert(
                $this->createAnnotation(['foo' => 'bar'])->foo(),
                equals('bar')
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
            [MyTestClass::TEST_CONSTANT, MyTestClass::class . '::TEST_CONSTANT']
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
        assert(
                $this->createAnnotation(['foo' => $stringValue])->foo(),
                equals($expected)
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
        assert(
                $this->createAnnotation(['foo' => $stringValue])->getFoo(),
                equals($expected)
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
        assert(
                $this->createAnnotation(['foo' => $stringValue])->getValueByName('foo'),
                equals($expected)
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
        assert(
                $this->createSingleValueAnnotation($stringValue)->getValue(),
                equals($expected)
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function canBeCastedToString()
    {
        assert(
                (string) $this->createAnnotation(['foo' => 303, 'bar' => "'value'"]),
                equals("@Life[Example](foo=303, bar='value')")
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
        assert(
                $this->createAnnotation(['foo' => $value])->parse('foo')->$type(),
                equals($expected)
        );
    }
}
