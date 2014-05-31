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
 * Test for stubbles\lang\reflect\MixedType.
 *
 * @since  3.1.1
 * @group  lang
 * @group  lang_reflect
 */
class MixedTypeTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return  array
     */
    public static function getTypes()
    {
        return array(array('string', false),
                     array('int', false),
                     array('integer', false),
                     array('float', false),
                     array('double', false),
                     array('bool', false),
                     array('boolean', false),
                     array('array', false),
                     array('mixed', true),
                     array('object', true),
                     array('void', false),
                     array('\stdClass', false),
        );
    }

    /**
     * @param  string  $type
     * @param  bool    $expected
     * @dataProvider  getTypes
     * @test
     */
    public function isKnownDeliversCorrectResult($type, $expected)
    {
        $this->assertEquals($expected,
                            MixedType::isKnown($type)
        );
    }

    /**
     * @test
     */
    public function mixedTypeCanBeObject()
    {
        $this->assertTrue(MixedType::forName('mixed')->isObject());
    }

    /**
     * @test
     */
    public function objectTypeIsObject()
    {
        $this->assertTrue(MixedType::forName('object')->isObject());
    }

    /**
     * @test
     */
    public function mixedTypeCanBePrimitive()
    {
        $this->assertTrue(MixedType::forName('mixed')->isPrimitive());
    }

    /**
     * @test
     */
    public function objectTypeIsNeverPrimitive()
    {
        $this->assertFalse(MixedType::forName('object')->isPrimitive());
    }

    /**
     * @test
     */
    public function mixedTypeAsString()
    {
        $this->assertEquals('stubbles\lang\\reflect\MixedType[mixed] ' . "{\n}\n",
                            (string) MixedType::forName('mixed')
        );
    }

    /**
     * @test
     */
    public function objectTypeAsString()
    {
        $this->assertEquals('stubbles\lang\\reflect\MixedType[object] ' . "{\n}\n",
                            (string) MixedType::forName('object')
        );
    }
}
