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
 * Tests for stubbles\lang\iterator\Peek.
 *
 * @group  lang
 * @group  lang_iterator
 * @group  sequence
 * @since  5.2.0
 */
class PeekTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function peekCallsValueConsumerWithCurrentValueOnIteration()
    {
        $result = '';
        $peek = new Peek(new \ArrayIterator(['foo', 'bar', 'baz']), function($value) use(&$result) { $result = $result . $value; });
        foreach ($peek as $value) {
            // do nothing
        }

        $this->assertEquals('foobarbaz', $result);
    }

    /**
     * @test
     */
    public function peekCallsKeyConsumerWithCurrentKeyOnIteration()
    {
        $result = '';
        $peek = new Peek(new \ArrayIterator(['foo' => 303, 'bar' => 404, 'baz' => 505]), function() { }, function($key) use(&$result) { $result = $result . $key; });
        foreach ($peek as $key => $value) {
            // do nothing
        }

        $this->assertEquals('foobarbaz', $result);
    }

    /**
     * @test
     */
    public function keyConsumerIsNotCalledWhenNoKeyInForeachRequested()
    {
        $i = 0;
        $peek = new Peek(new \ArrayIterator(['foo' => 303, 'bar' => 404, 'baz' => 505]), function() { }, function() { $this->fail('Key consumer is not expected to be called'); });
        foreach ($peek as $value) {
            $i++;
        }

        $this->assertEquals(3, $i);
    }
}
