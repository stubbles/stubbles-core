<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\iterator;
use function bovigo\assert\assert;
use function bovigo\assert\fail;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\lang\iterator\MappingIterator.
 *
 * @since  5.0.0
 * @group  lang
 * @group  lang_iterator
 */
class MappingIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  5.3.0
     */
    public function throwsInvalidArgumentExceptionWhenBothValueMapperAndKeyMapperAreNull()
    {
        new MappingIterator(new \ArrayIterator(['foo', 'bar', 'baz']));
    }

    /**
     * @test
     */
    public function mapsValueOnIteration()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo']),
                function($value) { return 'bar'; }
        );
        foreach ($mapping as $value) {
            assert($value, equals('bar'));
        }
    }

    /**
     * @test
     */
    public function valueMapperCanOptionallyReceiveKey()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 'bar']),
                function($value, $key) { return $key; }
        );
        foreach ($mapping as $value) {
            assert($value, equals('foo'));
        }
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function keyMapperCanOptionallyReceiveValue()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 'bar']),
                null,
                function($key, $value) { return $value; }
        );
        foreach ($mapping as $key => $value) {
            assert($key, equals('bar'));
        }
    }

    /**
     * @test
     */
    public function valueMapperReceivesUnmappedKey()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 'bar']),
                function($value, $key) { assert($key, equals('foo')); return 'mappedValue'; },
                function($key) { return 'mappedKey'; }
        );
        foreach ($mapping as $key => $value) {
            // intentionally empty
        }
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function keyMapperReceivesUnmappedValue()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 'bar']),
                function($value) { return 'mappedValue'; },
                function($key, $value) { assert($value, equals('bar')); return 'mappedKey'; }
        );
        foreach ($mapping as $key => $value) {
            // intentionally empty
        }
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function doesNotMapValueWhenNoValueMapperProvided()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 303, 'bar' => 808, 'baz' => '909']),
                null,
                function($value) { return 'mappedValue'; }
        );
        $values = [];
        foreach ($mapping as $key => $value) {
            $values[] = $value;
        }

        assert($values, equals([303, 808, '909']));
    }

    /**
     * @test
     */
    public function doesNotMapKeyWhenNoKeyMapperProvided()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 303, 'bar' => 808, 'baz' => '909']),
                function($value) { return 'mappedValue'; }
        );
        $keys = [];
        foreach ($mapping as $key => $value) {
            $keys[] = $key;
        }

        assert($keys, equals(['foo', 'bar', 'baz']));
    }

    /**
     * @test
     */
    public function mapsKeyWhenKeyMapperProvided()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 303, 'bar' => 808, 'baz' => 909]),
                function($value) { return 'mappedValue'; },
                function($key) { return 'mappedKey'; }
        );
        foreach ($mapping as $key => $value) {
            $keys[] = $key;
        }

        assert($keys, equals(['mappedKey', 'mappedKey', 'mappedKey']));
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function doesNotCallValueMapperWhenEndOfIteratorReached()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 303]),
                function() { fail('Should never be called'); }
        );
        $mapping->next();
        assertNull($mapping->current());
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function doesNotCallKeyMapperWhenEndOfIteratorReached()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 303]),
                function() { fail('Value mapper never be called'); },
                function() { fail('Key mapper never be called'); }
        );
        $mapping->next();
        assertNull($mapping->key());
    }
}