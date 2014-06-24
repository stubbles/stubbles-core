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
 * Test for stubbles\predicate\OrPredicate.
 *
 * @group  predicate
 * @since  4.0.0
 */
class OrPredicateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  OrPredicate
     */
    private $orPredicate;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->orPredicate = new OrPredicate(
                function($value) { return 'bar' === $value; },
                function($value) { return 'foo' === $value; }
        );
    }

    /**
     * @test
     */
    public function returnsTrueWhenOnePredicateReturnsTrue()
    {
        $this->assertTrue($this->orPredicate->test('foo'));
    }

    /**
     * @test
     */
    public function returnsFalseWhenBothPredicatesReturnsFalse()
    {
        $this->assertFalse($this->orPredicate->test('baz'));
    }
}
