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
/**
 * Test for stubbles\lang\reflect\ReflectionPrimitive.
 *
 * @group  lang
 * @group  lang_reflect
 * @deprecated  will be removed with 6.0.0
 */
class ReflectionPrimitiveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function stringPrimitive()
    {
        $primitive = ReflectionPrimitive::forName('string');
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionPrimitive',
                                $primitive
        );
        $this->assertEquals('string', $primitive->name());
        $this->assertEquals('string', $primitive->value());
        $this->assertEquals('string', $primitive->getName());
    }

    /**
     * @test
     */
    public function stringPrimitiveIsNotAnObject()
    {
        $primitive = ReflectionPrimitive::forName('string');
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
    }

    /**
     * @test
     */
    public function stringPrimitiveHasStringRepresentation()
    {
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionPrimitive[string] {\n}\n",
                            (string) ReflectionPrimitive::forName('string')
        );
    }

    /**
     * @test
     */
    public function stringPrimitiveIsEqualToItself()
    {
        $this->assertTrue(ReflectionPrimitive::forName('string')->equals(ReflectionPrimitive::forName('string')));
    }

    /**
     * @test
     */
    public function stringPrimitiveIsNotEqualToOtherType()
    {
        $this->assertFalse(ReflectionPrimitive::forName('string')->equals(new \stdClass()));
    }

    /**
     * @test
     */
    public function stringPrimitiveIsNotEqualToOtherPrimitives()
    {
        $primitive = ReflectionPrimitive::forName('string');
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('int')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('integer')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('float')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('double')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('bool')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('boolean')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('array')));
    }

    /**
     * @test
     */
    public function intPrimitive()
    {
        $primitive = ReflectionPrimitive::forName('int');
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionPrimitive', $primitive);
        $this->assertEquals('int', $primitive->name());
        $this->assertEquals('int', $primitive->getName());
        $this->assertEquals('int', $primitive->value());
    }

    /**
     * @test
     */
    public function integerPrimitive()
    {
        $primitive = ReflectionPrimitive::forName('integer');
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionPrimitive', $primitive);
        $this->assertEquals('integer', $primitive->name());
        $this->assertEquals('integer', $primitive->getName());
        $this->assertEquals('int', $primitive->value());
    }

    /**
     * @test
     */
    public function intPrimitiveIsNotAnObject()
    {
        $primitive = ReflectionPrimitive::forName('int');
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
    }

    /**
     * @test
     */
    public function intPrimitiveHasStringRepresentation()
    {
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionPrimitive[int] {\n}\n",
                            (string) ReflectionPrimitive::forName('int')
        );
    }

    /**
     * @test
     */
    public function integerPrimitiveHasStringRepresentation()
    {
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionPrimitive[int] {\n}\n",
                            (string) ReflectionPrimitive::forName('integer')
        );
    }

    /**
     * @test
     */
    public function intPrimitiveIsEqualToItself()
    {
        $this->assertTrue(ReflectionPrimitive::forName('int')->equals(ReflectionPrimitive::forName('int')));
    }

    /**
     * @test
     */
    public function intPrimitiveIsEqualToIntegerPrimitive()
    {
        $this->assertTrue(ReflectionPrimitive::forName('int')->equals(ReflectionPrimitive::forName('integer')));
        $this->assertTrue(ReflectionPrimitive::forName('integer')->equals(ReflectionPrimitive::forName('int')));
    }

    /**
     * @test
     */
    public function intPrimitiveIsNotEqualToOtherType()
    {
        $this->assertFalse(ReflectionPrimitive::forName('int')->equals(new \stdClass()));
    }

    /**
     * @test
     */
    public function intPrimitiveIsNotEqualToOtherPrimitives()
    {
        $primitive = ReflectionPrimitive::forName('int');
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('string')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('float')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('double')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('bool')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('boolean')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('array')));
    }

    /**
     * @test
     */
    public function floatPrimitive()
    {
        $primitive = ReflectionPrimitive::forName('float');
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionPrimitive', $primitive);
        $this->assertEquals('float', $primitive->name());
        $this->assertEquals('float', $primitive->getName());
        $this->assertEquals('float', $primitive->value());
    }

    /**
     * @test
     */
    public function doublePrimitive()
    {
        $primitive = ReflectionPrimitive::forName('double');
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionPrimitive', $primitive);
        $this->assertEquals('double', $primitive->name());
        $this->assertEquals('double', $primitive->getName());
        $this->assertEquals('float', $primitive->value());
    }

    /**
     * @test
     */
    public function floatPrimitiveIsNotAnObject()
    {
        $primitive = ReflectionPrimitive::forName('float');
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
    }

    /**
     * @test
     */
    public function doublePrimitiveIsNotAnObject()
    {
        $primitive = ReflectionPrimitive::forName('double');
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
    }

    /**
     * @test
     */
    public function floatPrimitiveHasStringRepresentation()
    {
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionPrimitive[float] {\n}\n",
                            (string) ReflectionPrimitive::forName('float')
        );
    }

    /**
     * @test
     */
    public function doublePrimitiveHasStringRepresentation()
    {
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionPrimitive[float] {\n}\n",
                            (string) ReflectionPrimitive::forName('double')
        );
    }

    /**
     * @test
     */
    public function floatPrimitiveIsEqualToItself()
    {
        $this->assertTrue(ReflectionPrimitive::forName('float')->equals(ReflectionPrimitive::forName('float')));
    }

    /**
     * @test
     */
    public function floatPrimitiveIsEqualToDoublePrimitive()
    {
        $this->assertTrue(ReflectionPrimitive::forName('float')->equals(ReflectionPrimitive::forName('double')));
        $this->assertTrue(ReflectionPrimitive::forName('double')->equals(ReflectionPrimitive::forName('float')));
    }

    /**
     * @test
     */
    public function floatPrimitiveIsNotEqualToOtherType()
    {
        $this->assertFalse(ReflectionPrimitive::forName('float')->equals(new \stdClass()));
    }

    /**
     * @test
     */
    public function floatPrimitiveIsNotEqualToOtherPrimitives()
    {
        $primitive = ReflectionPrimitive::forName('float');
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('string')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('int')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('integer')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('bool')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('boolean')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('array')));
    }

    /**
     * @test
     */
    public function boolPrimitive()
    {
        $primitive = ReflectionPrimitive::forName('bool');
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionPrimitive', $primitive);
        $this->assertEquals('bool', $primitive->name());
        $this->assertEquals('bool', $primitive->getName());
        $this->assertEquals('bool', $primitive->value());
    }

    /**
     * @test
     */
    public function booleanPrimitive()
    {
        $primitive = ReflectionPrimitive::forName('boolean');
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionPrimitive', $primitive);
        $this->assertEquals('boolean', $primitive->name());
        $this->assertEquals('boolean', $primitive->getName());
        $this->assertEquals('bool', $primitive->value());
    }

    /**
     * @test
     */
    public function boolPrimitiveIsNotAnObject()
    {
        $primitive = ReflectionPrimitive::forName('bool');
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
    }

    /**
     * @test
     */
    public function boolPrimitiveHasStringRepresentation()
    {
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionPrimitive[bool] {\n}\n",
                            (string) ReflectionPrimitive::forName('bool')
        );
    }

    /**
     * @test
     */
    public function booleanPrimitiveHasStringRepresentation()
    {
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionPrimitive[bool] {\n}\n",
                            (string) ReflectionPrimitive::forName('boolean')
        );
    }

    /**
     * @test
     */
    public function boolPrimitiveIsEqualToItself()
    {
        $this->assertTrue(ReflectionPrimitive::forName('bool')->equals(ReflectionPrimitive::forName('bool')));
    }

    /**
     * @test
     */
    public function boolPrimitiveIsEqualToBoolean()
    {
        $this->assertTrue(ReflectionPrimitive::forName('bool')->equals(ReflectionPrimitive::forName('boolean')));
        $this->assertTrue(ReflectionPrimitive::forName('boolean')->equals(ReflectionPrimitive::forName('bool')));
    }

    /**
     * @test
     */
    public function boolPrimitiveIsNotEqualToOtherType()
    {
        $this->assertFalse(ReflectionPrimitive::forName('bool')->equals(new \stdClass()));
    }

    /**
     * @test
     */
    public function boolPrimitiveIsNotEqualToOtherPrimitives()
    {
        $primitive = ReflectionPrimitive::forName('bool');
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('string')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('int')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('integer')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('float')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('double')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('array')));
    }

    /**
     * assure that array instance works as expected
     *
     * @test
     */
    public function arrayPrimitive()
    {
        $primitive = ReflectionPrimitive::forName('array');
        $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionPrimitive', $primitive);
        $this->assertEquals('array', $primitive->name());
        $this->assertEquals('array', $primitive->getName());
        $this->assertEquals('array', $primitive->value());
    }

    /**
     * @test
     */
    public function arrayPrimitiveIsNotAnObject()
    {
        $primitive = ReflectionPrimitive::forName('array');
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
    }

    /**
     * @test
     */
    public function arrayPrimitiveHasStringRepresentation()
    {
        $this->assertEquals("stubbles\\lang\\reflect\\ReflectionPrimitive[array] {\n}\n",
                            (string) ReflectionPrimitive::forName('array')
        );
    }

    /**
     * @test
     */
    public function arrayPrimitiveIsEqualToItself()
    {
        $this->assertTrue(ReflectionPrimitive::forName('array')->equals(ReflectionPrimitive::forName('array')));
    }

    /**
     * @test
     */
    public function arrayPrimitiveIsEqualToOtherArrayPrimitives()
    {
        $this->assertTrue(ReflectionPrimitive::forName('array<string,stdClass>')->equals(ReflectionPrimitive::forName('array')));
        $this->assertTrue(ReflectionPrimitive::forName('array')->equals(ReflectionPrimitive::forName('array<string,stdClass>')));
    }

    /**
     * @test
     */
    public function arrayPrimitiveIsNotEqualToOtherType()
    {
        $this->assertFalse(ReflectionPrimitive::forName('array')->equals(new \stdClass()));
    }

    /**
     * @test
     */
    public function arrayPrimitiveIsNotEqualToOtherPrimitives()
    {
        $primitive = ReflectionPrimitive::forName('array');
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('string')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('int')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('integer')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('float')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('double')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('bool')));
        $this->assertFalse($primitive->equals(ReflectionPrimitive::forName('boolean')));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function nonPrimitiveThrowsIllegalArgumentException()
    {
        ReflectionPrimitive::forName('\stdClass');
    }

    /**
     * @return  array
     */
    public static function getTypes()
    {
        return [['string', true],
                ['int', true],
                ['integer', true],
                ['float', true],
                ['double', true],
                ['bool', true],
                ['boolean', true],
                ['array', true],
                ['mixed', false],
                ['object', false],
                ['void', false],
                ['\stdClass', false],
        ];
    }

    /**
     * @since  3.1.1
     * @param  string  $type
     * @param  bool    $expected
     * @dataProvider  getTypes
     * @test
     */
    public function isKnownDeliversCorrectResult($type, $expected)
    {
        $this->assertEquals($expected,
                            ReflectionPrimitive::isKnown($type)
        );
    }
}
