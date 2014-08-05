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
use stubbles\lang\reflect\ReflectionClass;
use stubbles\peer\http\HttpUri;
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
 * Tests for stubbles\lang\Parse.
 *
 * @group  lang
 * @group  lang_core
 * @since  4.1.0
 */
class ParseTest extends \PHPUnit_Framework_TestCase
{
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
            [3, '3.14']
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
        $this->assertEquals($expectedResult, Parse::toInt($stringToParse));
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
            [3.14, '3.14']
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
        $this->assertEquals($expectedResult, Parse::toFloat($stringToParse));
    }

    /**
     * @return  array
     */
    public function stringToBoolConversions()
    {
        return [
            [true, '1'],
            [true, 'yes'],
            [true, 'true'],
            [true, 'on'],
            [false, '3.14'],
            [false, '0'],
            [false, 'no'],
            [false, 'false'],
            [false, 'off'],
            [false, 'other'],

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
        $this->assertEquals($expectedResult, Parse::toBool($stringToParse));
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
            [[], null],
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
        $this->assertEquals($expectedResult, Parse::toList($stringToParse));
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
            [[], null],
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
        $this->assertEquals($expectedResult, Parse::toMap($stringToParse));
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
            [[], null],
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
        $this->assertEquals($expectedResult, Parse::toRange($stringToParse));
    }

    /**
     * @return  array
     */
    public function stringToClassConversions()
    {
        return [
            [new ReflectionClass($this), __CLASS__ . '.class'],
            [new ReflectionClass('stubbles\lang\Mode'), 'stubbles\lang\Mode.class'],
            [null, null],
            [null, ''],
            [null, 'other']

        ];
    }

    /**
     * @param  \stubbles\lang\reflect\ReflectionClass  $expectedResult
     * @param  string                                  $stringToParse
     * @test
     * @dataProvider  stringToClassConversions
     */
    public function toClassReturnsValueCastedToClassInstance($expectedResult, $stringToParse)
    {
        $this->assertEquals($expectedResult, Parse::toClass($stringToParse));
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
     * @return  array
     */
    public function stringToEnumConversions()
    {
        return [
            [MyEnum::$FOO, 'stubbles\lang\MyEnum::$FOO'],
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
        $this->assertEquals($expectedResult, Parse::toEnum($stringToParse));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function toEnumWithNonExistingEnumThrowsIllegalArgumentException()
    {
        Parse::toEnum('stubbles\lang\MyEnum::$BAR');
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
            [true, '1'],
            [true, 'yes'],
            [true, 'true'],
            [true, 'on'],
            [false, '0'],
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
            [new ReflectionClass($this), __CLASS__ . '.class'],
            [MyEnum::$FOO, 'stubbles\lang\MyEnum::$FOO'],
            [MyEnum::TEST_CONSTANT, 'stubbles\lang\MyEnum::TEST_CONSTANT'],
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
        $this->assertEquals($expectedResult, Parse::toType($stringToParse));
    }

    /**
     * @test
     */
    public function userDefinedRecognitionWithSuccessReturnsValueFromUserDefinedConversion()
    {
        Parse::addRecognition(function($string) { if ('Binford 6100' === $string) { return 'More power!'; } });
        $this->assertEquals('More power!', Parse::toType('Binford 6100'));
    }

    /**
     * @test
     */
    public function userDefinedRecognitionWithoutSuccessReturnsValueAsString()
    {
        Parse::addRecognition(function($string) { if ('Binford 6100' === $string) { return 'More power!'; } });
        $this->assertEquals('Binford 610', Parse::toType('Binford 610'));
    }
}
