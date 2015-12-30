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
use function bovigo\assert\predicate\equals;
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
        assert(
                castToArray(new \ArrayIterator(['foo' => 'bar', 'baz' => 303])),
                equals(['foo' => 'bar', 'baz' => 303])
        );
    }

    /**
     * @test
     */
    public function castToArrayOnArray()
    {
        assert(
                castToArray(['foo' => 'bar', 'baz' => 303]),
                equals(['foo' => 'bar', 'baz' => 303])
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
        assert(castToArray($object), equals(['foo' => 'bar', 'baz' => 303]));
    }

    /**
     * @test
     */
    public function castToArrayOnObjectWithAsArrayMethod()
    {
        assert(
                castToArray(new AsArray()),
                equals(['foo' => 'bar', 'baz' => 303])
        );
    }

    /**
     * @test
     */
    public function castToArrayOnObjectWithToArrayMethod()
    {
        assert(
                castToArray(new ToArray()),
                equals(['foo' => 'bar', 'baz' => 303])
        );
    }

    /**
     * @test
     */
    public function castToArrayOnScalarValue()
    {
        assert(castToArray(303), equals([303]));
    }
}
