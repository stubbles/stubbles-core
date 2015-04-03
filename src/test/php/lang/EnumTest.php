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
/**
 * Concrete instance of stubbles\lang\Enum.
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
/**
 * Concrete instance of stubbles\lang\Enum.
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
/**
 * Tests for stubbles\lang\Enum.
 *
 * @group  lang
 * @group  lang_core
 */
class EnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        TestEnumWithoutValues::init();
        TestEnumWithValues::init();
    }

    /**
     * @test
     */
    public function nameEqualsGivenName()
    {
        $this->assertEquals('FOO', TestEnumWithoutValues::$FOO->name());
        $this->assertEquals('BAR', TestEnumWithoutValues::$BAR->name());
        $this->assertEquals('FOO', TestEnumWithValues::$FOO->name());
        $this->assertEquals('BAR', TestEnumWithValues::$BAR->name());
    }

    /**
     * @test
     */
    public function valueEqualsNameIfNoValueGiven()
    {
        $this->assertEquals('FOO', TestEnumWithoutValues::$FOO->value());
        $this->assertEquals('BAR', TestEnumWithoutValues::$BAR->value());
    }

    /**
     * @test
     */
    public function valueEqualsGivenValue()
    {
        $this->assertEquals(10, TestEnumWithValues::$FOO->value());
        $this->assertEquals(20, TestEnumWithValues::$BAR->value());
    }

    /**
     * @test
     */
    public function instanceEqualsItself()
    {
        $this->assertTrue(TestEnumWithoutValues::$FOO->equals(TestEnumWithoutValues::$FOO));
    }

    /**
     * @test
     */
    public function instanceDoesNotEqualOtherInstance()
    {
        $this->assertFalse(TestEnumWithoutValues::$FOO->equals(TestEnumWithoutValues::$BAR));
        $this->assertFalse(TestEnumWithoutValues::$BAR->equals(TestEnumWithoutValues::$FOO));
    }

    /**
     * @test
     */
    public function instanceDoesNotEqualNonEnumType()
    {
        $this->assertFalse(TestEnumWithoutValues::$BAR->equals(new \stdClass()));
    }

    /**
     * @test
     */
    public function toStringReturnsRepresentationWithoutValues()
    {
        $this->assertEquals("stubbles\lang\TestEnumWithoutValues {\n    FOO\n    FOO\n}\n",
                            (string) TestEnumWithoutValues::$FOO
        );
        $this->assertEquals("stubbles\lang\TestEnumWithoutValues {\n    BAR\n    BAR\n}\n",
                            (string) TestEnumWithoutValues::$BAR
        );
    }

    /**
     * @test
     */
    public function toStringReturnsRepresentationWithValues()
    {
        $this->assertEquals("stubbles\lang\TestEnumWithValues {\n    FOO\n    10\n}\n",
                            (string) TestEnumWithValues::$FOO
        );
        $this->assertEquals("stubbles\lang\TestEnumWithValues {\n    BAR\n    20\n}\n",
                            (string) TestEnumWithValues::$BAR
        );
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
        $this->assertSame(TestEnumWithoutValues::$FOO, TestEnumWithoutValues::forName('FOO'));
        $this->assertSame(TestEnumWithoutValues::$BAR, TestEnumWithoutValues::forName('BAR'));
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
        $this->assertSame(TestEnumWithoutValues::$FOO, TestEnumWithoutValues::forValue('FOO'));
        $this->assertSame(TestEnumWithoutValues::$BAR, TestEnumWithoutValues::forValue('BAR'));
    }

    /**
     * assure that forValue() works as expected
     *
     * @test
     */
    public function forValueWithValues()
    {
        $this->assertSame(TestEnumWithValues::$FOO, TestEnumWithValues::forValue(10));
        $this->assertSame(TestEnumWithValues::$BAR, TestEnumWithValues::forValue(20));
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
        $this->assertEquals([TestEnumWithoutValues::$FOO, TestEnumWithoutValues::$BAR],
                            TestEnumWithoutValues::instances()
        );
    }

    /**
     * @test
     */
    public function namesOfReturnsListOfNamesOfEnum()
    {
        $this->assertEquals(['FOO', 'BAR'], TestEnumWithoutValues::namesOf());
    }

    /**
     * @test
     */
    public function valuesOfWithoutValuesReturnsMapOfValues()
    {
        $this->assertEquals(['FOO' => 'FOO', 'BAR' => 'BAR'],
                            TestEnumWithoutValues::valuesOf()
        );
    }

    /**
     * @test
     */
    public function valuesOfWithValuesReturnsMapOfValues()
    {
        $this->assertEquals(['FOO' => 10, 'BAR' => 20],
                            TestEnumWithValues::valuesOf()
        );
    }
}
