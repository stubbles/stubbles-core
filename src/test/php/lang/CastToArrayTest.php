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
 * Helper class for the test.
 */
class ToArray
{
    /**
     * @return  array
     */
    public function toArray()
    {
        return ['foo' => 'bar', 'baz' => 303];
    }
}
/**
 * Helper class for the test.
 */
class AsArray
{
    /**
     * @return  array
     */
    public function asArray()
    {
        return ['foo' => 'bar', 'baz' => 303];
    }
}
/**
 * Tests for stubbles\lang\castToArray().
 *
 * @since  5.4.0
 * @group  lang
 * @group  lang_core
 */
class CastToArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function castToArrayOnTraversable()
    {
        $this->assertEquals(
                ['foo' => 'bar', 'baz' => 303],
                castToArray(new \ArrayIterator(['foo' => 'bar', 'baz' => 303]))
        );
    }

    /**
     * @test
     */
    public function castToArrayOnArray()
    {
        $this->assertEquals(
                ['foo' => 'bar', 'baz' => 303],
                castToArray(['foo' => 'bar', 'baz' => 303])
        );
    }

    /**
     * @test
     */
    public function castToArrayOnObject()
    {
        $object = new \stdClass();
        $object->foo = 'bar';
        $object->baz = 303;
        $this->assertEquals(
                ['foo' => 'bar', 'baz' => 303],
                castToArray($object)
        );
    }

    /**
     * @test
     */
    public function castToArrayOnObjectWithAsArrayMethod()
    {
        $this->assertEquals(
                ['foo' => 'bar', 'baz' => 303],
                castToArray(new AsArray())
        );
    }

    /**
     * @test
     */
    public function castToArrayOnObjectWithToArrayMethod()
    {
        $this->assertEquals(
                ['foo' => 'bar', 'baz' => 303],
                castToArray(new ToArray())
        );
    }

    /**
     * @test
     */
    public function castToArrayOnScalarValue()
    {
        $this->assertEquals(
                [303],
                castToArray(303)
        );
    }
}
