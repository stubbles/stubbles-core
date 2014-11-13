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
     */
    public function mapsValueOnIteration()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo', 'bar', 'baz']),
                function($value) { return 'mappedValue'; }
        );
        foreach ($mapping as $value) {
            $this->assertEquals('mappedValue', $value);
        }
    }

    /**
     * @test
     */
    public function valueMapperCanOptionallyReceiveKey()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz']),
                function($value, $key) { $this->assertEquals($value, $key); return 'mappedValue'; }
        );
        foreach ($mapping as $value) {
            // intentionally empty
        }
    }

    /**
     * @test
     */
    public function valueMapperReceivesUnmappedKey()
    {
        $mapping = new MappingIterator(
                new \ArrayIterator(['foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz']),
                function($value, $key) { $this->assertEquals($value, $key); return 'mappedValue'; },
                function($key) { return 'mappedKey'; }
        );
        foreach ($mapping as $key => $value) {
            // intentionally empty
        }
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

        $this->assertEquals(['foo', 'bar', 'baz'], $keys);
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
            $this->assertEquals('mappedKey', $key);
        }
    }
}