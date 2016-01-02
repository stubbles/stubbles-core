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
use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isSameAs;
/**
 * Concrete instance of stubbles\lang\Enum.
 *
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class TestEnumWithoutValues extends Enum
{
    /**
     * foo instance
     *
     * @type  TestEnumWithoutValues
     */
    public static $FOO;
    /**
     * bar instance
     *
     * @type  TestEnumWithoutValues
     */
    public static $BAR;

    /**
     * helper method to initialize as we have no __static() call available in tests
     */
    public static function init()
    {
        self::$FOO = new self('FOO');
        self::$BAR = new self('BAR');
    }
}
TestEnumWithoutValues::init();
/**
 * Concrete instance of stubbles\lang\Enum.
 *
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class TestEnumWithValues extends Enum
{
    /**
     * foo instance
     *
     * @type  TestEnumWithoutValues
     */
    public static $FOO;
    /**
     * bar instance
     *
     * @type  TestEnumWithoutValues
     */
    public static $BAR;

    /**
     * helper method to initialize as we have no __static() call available in tests
     */
    public static function init()
    {
        self::$FOO = new self('FOO', 10);
        self::$BAR = new self('BAR', 20);
    }
}
TestEnumWithValues::init();
/**
 * Tests for stubbles\lang\Enum.
 *
 * @group  lang
 * @group  lang_core
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class EnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return  array
     */
    public function enumValues()
    {
        return [
                ['FOO', TestEnumWithoutValues::class],
                ['BAR', TestEnumWithoutValues::class],
                ['FOO', TestEnumWithValues::class],
                ['BAR', TestEnumWithValues::class]
        ];
    }

    /**
     * @test
     * @dataProvider  enumValues
     */
    public function nameEqualsGivenName($name, $enumClass)
    {
        assert($enumClass::$$name->name(), equals($name));
    }

    /**
     * @test
     */
    public function valueEqualsNameIfNoValueGiven()
    {
        assert(TestEnumWithoutValues::$FOO->value(), equals('FOO'));
    }

    /**
     * @test
     */
    public function valueEqualsGivenValue()
    {
        assert(TestEnumWithValues::$FOO->value(), equals(10));
    }

    /**
     * @test
     */
    public function instanceEqualsItself()
    {
        assertTrue(
                TestEnumWithoutValues::$FOO->equals(TestEnumWithoutValues::$FOO)
        );
    }

    /**
     * @test
     */
    public function instanceDoesNotEqualOtherInstance()
    {
        assertFalse(
                TestEnumWithoutValues::$FOO->equals(TestEnumWithoutValues::$BAR)
        );
        assertFalse(
                TestEnumWithoutValues::$BAR->equals(TestEnumWithoutValues::$FOO)
        );
    }

    /**
     * @test
     */
    public function instanceDoesNotEqualNonEnumType()
    {
        assertFalse(TestEnumWithoutValues::$BAR->equals(new \stdClass()));
    }

    /**
     * @return  array
     */
    public function stringRepresentations()
    {
        return [
                [TestEnumWithoutValues::$FOO, TestEnumWithoutValues::class . " {\n    FOO\n    FOO\n}\n"],
                [TestEnumWithoutValues::$BAR, TestEnumWithoutValues::class . " {\n    BAR\n    BAR\n}\n"],
                [TestEnumWithValues::$FOO, TestEnumWithValues::class . " {\n    FOO\n    10\n}\n"],
                [TestEnumWithValues::$BAR, TestEnumWithValues::class . " {\n    BAR\n    20\n}\n"]
        ];
    }

    /**
     * @test
     * @dataProvider  stringRepresentations
     */
    public function toStringReturnsRepresentation($instance, $representation)
    {
        assert((string) $instance, equals($representation));
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function cloningEnumsIsNotAllowed()
    {
        $foo = clone (TestEnumWithoutValues::$FOO);
    }

    /**
     * @test
     */
    public function forNameReturnsEnumInstanceWithGivenName()
    {
        assert(
                TestEnumWithoutValues::forName('FOO'),
                isSameAs(TestEnumWithoutValues::$FOO)
        );
    }

    /**
     * assure that forName() works as expected
     *
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function forNameWithNonExistingNameThrowsIllegalArgumentException()
    {
        TestEnumWithoutValues::forName('BAZ');
    }

    /**
     * assure that forValue() works as expected
     *
     * @test
     */
    public function forValueWithoutValues()
    {
        assert(
                TestEnumWithoutValues::forValue('FOO'),
                isSameAs(TestEnumWithoutValues::$FOO)
        );
    }

    /**
     * assure that forValue() works as expected
     *
     * @test
     */
    public function forValueWithValues()
    {
        assert(
                TestEnumWithValues::forValue(10),
                isSameAs(TestEnumWithValues::$FOO)
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function forValueWithNonExistingValueWithoutValuesThrowsIllegalArgumentException()
    {
        TestEnumWithoutValues::forValue('BAZ');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function forValueWithNonExistingValueWithValuesThrowsIllegalArgumentException()
    {
        TestEnumWithValues::forValue(404);
    }

    /**
     * @test
     */
    public function instancesReturnsListOfAllEnumInstances()
    {
        assert(
                TestEnumWithoutValues::instances(),
                equals([TestEnumWithoutValues::$FOO, TestEnumWithoutValues::$BAR])
        );
    }

    /**
     * @test
     */
    public function namesOfReturnsListOfNamesOfEnum()
    {
        assert(TestEnumWithoutValues::namesOf(), equals(['FOO', 'BAR']));
    }

    /**
     * @test
     */
    public function valuesOfWithoutValuesReturnsMapOfValues()
    {
        assert(
                TestEnumWithoutValues::valuesOf(),
                equals(['FOO' => 'FOO', 'BAR' => 'BAR'])
        );
    }

    /**
     * @test
     */
    public function valuesOfWithValuesReturnsMapOfValues()
    {
        assert(
                TestEnumWithValues::valuesOf(),
                equals(['FOO' => 10, 'BAR' => 20])
        );
    }
}
