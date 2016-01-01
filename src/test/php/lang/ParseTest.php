<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use stubbles\peer\http\HttpUri;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Helper class for the test.
 */
class MyEnum extends Enum
{
    const TEST_CONSTANT = 'baz';
    public static $FOO;

    public static function __static()
    {
        self::$FOO = new self('FOO');
    }
}
MyEnum::__static();
/**
 * Helper interface for the test.
 */
interface SomeInterface
{
    // intentionally empty
}
/**
 * Tests for stubbles\lang\Parse.
 *
 * @group  lang
 * @group  lang_core
 * @since  4.1.0
 */
class ParseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * clean up test environment
     */
    public function tearDown()
    {
        Parse::removeRecognition('binford');
        Parse::__static();
    }

    /**
     * @return  array
     */
    public function stringToIntConversions()
    {
        return [
            [0, '0'],
            [1, '1'],
            [-303, '-303'],
            [80, '80foo'],
            [3, '3.14'],
            [0, ''],
            [null, null]
        ];
    }

    /**
     * @param  int     $expectedResult
     * @param  string  $stringToParse
     * @test
     * @dataProvider  stringToIntConversions
     */
    public function toIntReturnsValueCastedToInteger($expectedResult, $stringToParse)
    {
        assert(Parse::toInt($stringToParse), equals($expectedResult));
    }

    /**
     * @param  int     $expectedResult
     * @param  string  $stringToParse
     * @test
     * @dataProvider  stringToIntConversions
     * @since  5.0.0
     */
    public function asIntReturnsValueCastedToInteger($expectedResult, $stringToParse)
    {
        $parse = new Parse($stringToParse);
        assert($parse->asInt(), equals($expectedResult));
    }

    /**
     * @param  int     $expectedResult
     * @param  string  $stringToParse
     * @test
     * @dataProvider  stringToIntConversions
     * @since  5.0.0
     */
    public function asIntWithDefaultReturnsValueCastedToInteger($expectedResult, $stringToParse)
    {
        if (null === $stringToParse) {
            $expectedResult = 'foo';
        }

        $parse = new Parse($stringToParse);
        assert($parse->defaultingTo('foo')->asInt(), equals($expectedResult));
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function toIntOnNullReturnsNull()
    {
        assertNull(Parse::toInt(null));
    }

    /**
     * @return  array
     */
    public function stringToFloatConversions()
    {
        return [
            [0.1, '0.1'],
            [1, '1'],
            [-3.03, '-3.03'],
            [8.0, '8.0foo'],
            [3.14, '3.14'],
            [0, ''],
            [null, null]
        ];
    }

    /**
     * @param  float   $expectedResult
     * @param  string  $stringToParse
     * @test
     * @dataProvider  stringToFloatConversions
     */
    public function toFloatReturnsValueCastedToFloat($expectedResult, $stringToParse)
    {
        assert(Parse::toFloat($stringToParse), equals($expectedResult));
    }

    /**
     * @param  float   $expectedResult
     * @param  string  $stringToParse
     * @test
     * @dataProvider  stringToFloatConversions
     * @since  5.0.0
     */
    public function asFloatReturnsValueCastedToFloat($expectedResult, $stringToParse)
    {
        $parse = new Parse($stringToParse);
        assert($parse->asFloat(), equals($expectedResult));
    }

    /**
     * @param  float   $expectedResult
     * @param  string  $stringToParse
     * @test
     * @dataProvider  stringToFloatConversions
     * @since  5.0.0
     */
    public function asFloatWithDefaultReturnsValueCastedToFloat($expectedResult, $stringToParse)
    {
        if (null === $stringToParse) {
            $expectedResult = 'foo';
        }

        $parse = new Parse($stringToParse);
        assert($parse->defaultingTo('foo')->asFloat(), equals($expectedResult));
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function toFloatOnNullReturnsNull()
    {
        assertNull(Parse::toFloat(null));
    }

    /**
     * @return  array
     */
    public function stringToBoolConversions()
    {
        return [
            [true, 'yes'],
            [true, 'true'],
            [true, 'on'],
            [false, '3.14'],
            [false, 'no'],
            [false, 'false'],
            [false, 'off'],
            [false, 'other'],
            [false, ''],
            [null, null],

        ];
    }

    /**
     * @param  bool    $expectedResult
     * @param  string  $stringToParse
     * @test
     * @dataProvider  stringToBoolConversions
     */
    public function toBoolReturnsValueCastedToBool($expectedResult, $stringToParse)
    {
        assert(Parse::toBool($stringToParse), equals($expectedResult));
    }

    /**
     * @param  bool    $expectedResult
     * @param  string  $stringToParse
     * @test
     * @dataProvider  stringToBoolConversions
     * @since  5.0.0
     */
    public function asBoolReturnsValueCastedToBool($expectedResult, $stringToParse)
    {
        $parse = new Parse($stringToParse);
        assert($parse->asBool($stringToParse), equals($expectedResult));
    }

    /**
     * @param  bool    $expectedResult
     * @param  string  $stringToParse
     * @test
     * @dataProvider  stringToBoolConversions
     * @since  5.0.0
     */
    public function asBoolWithDefaultReturnsValueCastedToBool($expectedResult, $stringToParse)
    {
        if (null === $stringToParse) {
            $expectedResult = 'foo';
        }

        $parse = new Parse($stringToParse);
        assert(
                $parse->defaultingTo('foo')->asBool($stringToParse),
                equals($expectedResult)
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function toBoolOnNullReturnsNull()
    {
        assertNull(Parse::toBool(null));
    }

    /**
     * @return  array
     */
    public function stringToListConversions()
    {
        return [
            [['foo', 'bar', 'baz'], 'foo|bar|baz'],
            [['foo', 'bar', 'baz'], '[foo|bar|baz]'],
            [[], ''],
            [[], '[]'],
            [null, null],
            [['', ''], '|'],
            [['', ''], '[|]'],
            [['foo'], 'foo'],
            [['foo'], '[foo]']

        ];
    }

    /**
     * @param  string[]  $expectedResult
     * @param  string    $stringToParse
     * @test
     * @dataProvider  stringToListConversions
     */
    public function toListReturnsValueCastedToList($expectedResult, $stringToParse)
    {
        assert(Parse::toList($stringToParse), equals($expectedResult));
    }

    /**
     * @param  string[]  $expectedResult
     * @param  string    $stringToParse
     * @test
     * @dataProvider  stringToListConversions
     * @since  5.0.0
     */
    public function asListReturnsValueCastedToList($expectedResult, $stringToParse)
    {
        $parse = new Parse($stringToParse);
        assert($parse->asList($stringToParse), equals($expectedResult));
    }

    /**
     * @param  string[]  $expectedResult
     * @param  string    $stringToParse
     * @test
     * @dataProvider  stringToListConversions
     * @since  5.0.0
     */
    public function asListWithDefaultReturnsValueCastedToList($expectedResult, $stringToParse)
    {
        if (null === $stringToParse) {
            $expectedResult = 'foo';
        }

        $parse = new Parse($stringToParse);
        assert(
                $parse->defaultingTo('foo')->asList($stringToParse),
                equals($expectedResult)
        );
    }

    /**
     * @return  array
     */
    public function stringToMapConversions()
    {
        return [
            [['foo', 'bar', 'baz'], 'foo|bar|baz'],
            [['foo', 'bar', 'baz'], '[foo|bar|baz]'],
            [['foo' => 'bar', 'baz' => 'dummy'], 'foo:bar|baz:dummy'],
            [['foo' => 'bar', 'baz' => 'dummy'], '[foo:bar|baz:dummy]'],
            [['foo' => 'bar', 'baz'], 'foo:bar|baz'],
            [['foo' => 'bar', 'baz'], '[foo:bar|baz]'],
            [[], ''],
            [[], '[]'],
            [null, null],
            [['', ''], '|'],
            [['', ''], '[|]'],
            [['foo'], 'foo'],
            [['foo'], '[foo]'],
            [['foo' => 'baz'], 'foo:baz'],
            [['foo' => 'baz'], '[foo:baz]']

        ];
    }

    /**
     * @param  array   $expectedResult
     * @param  string   $stringToParse
     * @test
     * @dataProvider  stringToMapConversions
     */
    public function toMapReturnsValueCastedToMap($expectedResult, $stringToParse)
    {
        assert(Parse::toMap($stringToParse), equals($expectedResult));
    }

    /**
     * @param  array   $expectedResult
     * @param  string   $stringToParse
     * @test
     * @dataProvider  stringToMapConversions
     * @since  5.0.0
     */
    public function asMapReturnsValueCastedToMap($expectedResult, $stringToParse)
    {
        $parse = new Parse($stringToParse);
        assert($parse->asMap($stringToParse), equals($expectedResult));
    }

    /**
     * @param  array   $expectedResult
     * @param  string   $stringToParse
     * @test
     * @dataProvider  stringToMapConversions
     * @since  5.0.0
     */
    public function asMapWithDefaultReturnsValueCastedToMap($expectedResult, $stringToParse)
    {
        if (null === $stringToParse) {
            $expectedResult = 'foo';
        }

        $parse = new Parse($stringToParse);
        assert(
                $parse->defaultingTo('foo')->asMap($stringToParse),
                equals($expectedResult)
        );
    }

    /**
     * @return  array
     */
    public function stringToRangeConversions()
    {
        return [
            [[1, 2, 3, 4, 5], '1..5'],
            [['a', 'b', 'c', 'd', 'e'], 'a..e'],
            [[], '1..'],
            [[], 'a..'],
            [[], '..5'],
            [[], '..e'],
            [[5, 4, 3, 2, 1], '5..1'],
            [['e', 'd', 'c', 'b', 'a'], 'e..a'],
            [[], ''],
            [null, null],
            [[], 'other']

        ];
    }

    /**
     * @param  mixed[]  $expectedResult
     * @param  string   $stringToParse
     * @test
     * @dataProvider  stringToRangeConversions
     */
    public function toRangeReturnsValueCastedToRange($expectedResult, $stringToParse)
    {
        assert(Parse::toRange($stringToParse), equals($expectedResult));
    }

    /**
     * @param  mixed[]  $expectedResult
     * @param  string   $stringToParse
     * @test
     * @dataProvider  stringToRangeConversions
     * @since  5.0.0
     */
    public function asRangeReturnsValueCastedToRange($expectedResult, $stringToParse)
    {
        $parse = new Parse($stringToParse);
        assert($parse->asRange($stringToParse), equals($expectedResult));
    }

    /**
     * @param  mixed[]  $expectedResult
     * @param  string   $stringToParse
     * @test
     * @dataProvider  stringToRangeConversions
     * @since  5.0.0
     */
    public function asRangeWithDefaultReturnsValueCastedToRange($expectedResult, $stringToParse)
    {
        if (null === $stringToParse) {
            $expectedResult = 'foo';
        }

        $parse = new Parse($stringToParse);
        assert(
                $parse->defaultingTo('foo')->asRange($stringToParse),
                equals($expectedResult)
        );
    }

    /**
     * @return  array
     */
    public function stringToClassConversions()
    {
        return [
            [new \ReflectionClass($this), __CLASS__ . '.class'],
            [new \ReflectionClass(SomeInterface::class), SomeInterface::class . '.class'],
            [null, null],
            [null, ''],
            [null, 'other']

        ];
    }

    /**
     * @param  \ReflectionClass  $expectedResult
     * @param  string            $stringToParse
     * @test
     * @dataProvider  stringToClassConversions
     */
    public function toClassReturnsValueCastedToClassInstance($expectedResult, $stringToParse)
    {
        assert(Parse::toClass($stringToParse), equals($expectedResult));
    }

    /**
     * @param  \ReflectionClass  $expectedResult
     * @param  string            $stringToParse
     * @test
     * @dataProvider  stringToClassConversions
     * @since  5.0.0
     */
    public function asClassReturnsValueCastedToClassInstance($expectedResult, $stringToParse)
    {
        $parse = new Parse($stringToParse);
        assert($parse->asClass($stringToParse), equals($expectedResult));
    }

    /**
     * @param  \ReflectionClass  $expectedResult
     * @param  string            $stringToParse
     * @test
     * @dataProvider  stringToClassConversions
     * @since  5.0.0
     */
    public function asClassWithDefaultReturnsValueCastedToClassInstance($expectedResult, $stringToParse)
    {
        if (null === $stringToParse) {
            $expectedResult = 'foo';
        }

        $parse = new Parse($stringToParse);
        assert($parse->defaultingTo('foo')->asClass($stringToParse), equals($expectedResult));
    }

    /**
     * @test
     * @expectedException  ReflectionException
     */
    public function toClassWithNonExistingClassThrowsReflectionException()
    {
        Parse::toClass('does\not\Exist.class');
    }

    /**
     * @test
     * @expectedException  ReflectionException
     * @since  5.0.0
     */
    public function asClassWithNonExistingClassThrowsReflectionException()
    {
        $parse = new Parse('does\not\Exist.class');
        $parse->asClass();
    }

    /**
     * @test
     * @expectedException  ReflectionException
     * @since  5.0.0
     */
    public function asClassWithNonExistingClassAndDefaultThrowsReflectionException()
    {
        $parse = new Parse('does\not\Exist.class');
        $parse->defaultingTo(__CLASS__ . '.class')->asClass();
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function toClassnameReturnsNullForNull()
    {
        assertNull(Parse::toClassname(null));
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function toClassnameReturnsNullForEmptyString()
    {
        assertNull(Parse::toClassname(''));
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function toClassnameReturnsNullForNonExistingClass()
    {
        assertNull(Parse::toClassname('does\not\Exist::class'));
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function toClassnameReturnsClassnameOfExistingClass()
    {
        assert(
                Parse::toClassname(__CLASS__ . '::class'),
                equals(__CLASS__)
        );
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function asClassnameReturnsNullForNull()
    {
        $parse = new Parse(null);
        assertNull($parse->asClassname());
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function asClassnameReturnsNullForEmptyString()
    {
        $parse = new Parse('');
        assertNull($parse->asClassname());
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function asClassnameReturnsNullForNonExistingClass()
    {
        $parse = new Parse('does\not\Exist::class');
        assertNull($parse->asClassname());
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function asClassnameReturnsClassnameOfExistingClass()
    {
        $parse = new Parse(__CLASS__ . '::class');
        assert($parse->asClassname(), equals(__CLASS__));
    }

    /**
     * @return  array
     */
    public function stringToEnumConversions()
    {
        return [
            [MyEnum::$FOO, MyEnum::class . '::$FOO'],
            [null, null],
            [null, ''],
            [null, 'other']

        ];
    }

    /**
     * @param  \stubbles\lang\Enum  $expectedResult
     * @param  string               $stringToParse
     * @test
     * @dataProvider  stringToEnumConversions
     */
    public function toEnumReturnsValueCastedToEnumInstance($expectedResult, $stringToParse)
    {
        assert(Parse::toEnum($stringToParse), equals($expectedResult));
    }

    /**
     * @param  \stubbles\lang\Enum  $expectedResult
     * @param  string               $stringToParse
     * @test
     * @dataProvider  stringToEnumConversions
     * @since  5.0.0
     */
    public function asEnumReturnsValueCastedToEnumInstance($expectedResult, $stringToParse)
    {
        $parse = new Parse($stringToParse);
        assert($parse->asEnum($stringToParse), equals($expectedResult));
    }

    /**
     * @param  \stubbles\lang\Enum  $expectedResult
     * @param  string               $stringToParse
     * @test
     * @dataProvider  stringToEnumConversions
     * @since  5.0.0
     */
    public function asEnumWithDefaultReturnsValueCastedToEnumInstance($expectedResult, $stringToParse)
    {
        if (null === $stringToParse) {
            $expectedResult = 'foo';
        }

        $parse = new Parse($stringToParse);
        assert($parse->defaultingTo('foo')->asEnum($stringToParse), equals($expectedResult));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function toEnumWithNonExistingEnumThrowsIllegalArgumentException()
    {
        Parse::toEnum(MyEnum::class . '::$BAR');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  5.0.0
     */
    public function asEnumWithNonExistingEnumThrowsIllegalArgumentException()
    {
        $parse = new Parse(MyEnum::class . '::$BAR');
        $parse->asEnum();
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Class stubbles\lang\MyEnum does not have a property named BAR
     * @since  5.0.0
     */
    public function asEnumWithNonExistingEnumAndDefaultThrowsIllegalArgumentException()
    {
        $parse = new Parse(MyEnum::class . '::$BAR');
        $parse->defaultingTo('foo')->asEnum();
    }

    /**
     * @return  array
     */
    public function stringToTypeConversions()
    {
        return [
            [null, null],
            ['', ''],
            [null, 'null'],
            [1, '1'],
            [true, 'yes'],
            [true, 'true'],
            [true, 'on'],
            [0, '0'],
            [false, 'no'],
            [false, 'false'],
            [false, 'off'],
            [303, '303'],
            [-303, '-303'],
            [3.03, '3.03'],
            [-3.03, '-3.03'],
            [['foo' => 'bar', 'baz'], '[foo:bar|baz]'],
            [['foo', 'bar', 'baz'], '[foo|bar|baz]'],
            [[1, 2, 3, 4, 5], '1..5'],
            [['a', 'b', 'c', 'd', 'e'], 'a..e'],
            [HttpUri::fromString('http://example.net/'), 'http://example.net/'],
            [new \ReflectionClass($this), __CLASS__ . '.class'],
            [MyEnum::$FOO, MyEnum::class . '::$FOO'],
            [MyEnum::TEST_CONSTANT, MyEnum::class . '::TEST_CONSTANT'],
            ['just a string', 'just a string']
        ];
    }

    /**
     * @param type $expectedResult
     * @param type $stringToParse
     * @test
     * @dataProvider  stringToTypeConversions
     */
    public function toTypeReturnsValueCastedToRecognizedType($expectedResult, $stringToParse)
    {
        assert(Parse::toType($stringToParse), equals($expectedResult));
    }

    /**
     * @test
     */
    public function userDefinedRecognitionWithSuccessReturnsValueFromUserDefinedConversion()
    {
        Parse::addRecognition(function($string) { if ('Binford 6100' === $string) { return 'More power!'; } }, 'binford');
        assert(Parse::toType('Binford 6100'), equals('More power!'));
    }

    /**
     * @test
     */
    public function userDefinedRecognitionWithoutSuccessReturnsValueAsString()
    {
        Parse::addRecognition(function($string) { if ('Binford 6100' === $string) { return 'More power!'; } }, 'binford');
        assert(Parse::toType('Binford 610'), equals('Binford 610'));
    }

    /**
     * @test
     */
    public function canReplaceExistingRecognition()
    {
        Parse::addRecognition(function($string) { if ('Binford 6100' === $string) { return true; } }, 'booleanTrue');
        assertTrue(Parse::toType('Binford 6100'));
    }

    /**
     * @return  array
     */
    public function methods()
    {
        return [
            [null, 'asString'],
            [0, 'asInt'],
            [0, 'asFloat'],
            [false, 'asBool'],
            [null, 'asList'],
            [null, 'asMap'],
            [null, 'asRange'],
            [null, 'asClass'],
            [null, 'asEnum'],
        ];
    }

    /**
     *
     * @param  mixed   $expected
     * @param  string  $method
     * @test
     * @dataProvider  methods
     * @since  5.0.0
     */
    public function parseNullReturnsNull($expected, $method)
    {
        $parse = new Parse(null);
        assert($parse->$method(), equals($expected));
    }

    /**
     *
     * @param  mixed   $expected
     * @param  string  $method
     * @test
     * @dataProvider  methods
     * @since  5.0.0
     */
    public function parseNullWithDefaultReturnsDefault($expected, $method)
    {
        $parse = new Parse(null);
        assert($parse->defaultingTo('foo')->$method(), equals('foo'));
    }
}
