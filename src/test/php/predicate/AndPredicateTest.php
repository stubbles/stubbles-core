<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\predicate;
/**
 * Test for stubbles\predicate\AndPredicate.
 *
 * @group  predicate
 * @since  4.0.0
 */
class AndPredicateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AndPredicate
     */
    private $andPredicate;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->andPredicate = new AndPredicate(
                function($value) { return 'foo' === $value; },
                function($value) { return 'foo' === $value; }
        );
    }

    /**
     * @test
     */
    public function returnsTrueWhenBothPredicatesReturnsTrue()
    {
        $this->assertTrue($this->andPredicate->test('foo'));
    }

    /**
     * @test
     */
    public function returnsFalseWhenOnePredicateReturnsFalse()
    {
        $this->assertFalse($this->andPredicate->test('baz'));
    }
}
