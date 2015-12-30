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
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
/**
 * Test for stubbles\predicate\NegatePredicate.
 *
 * @group  predicate
 * @since  4.0.0
 */
class NegatePredicateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function createNegatePredicate()
    {
        return new NegatePredicate(
                function($value) { return 'foo' === $value; }
        );
    }

    /**
     * @test
     */
    public function falseBecomesTrue()
    {
        $negatePredicate = $this->createNegatePredicate();
        assertTrue($negatePredicate('bar'));
    }

    /**
     * @test
     */
    public function trueBecomesFalse()
    {
        $negatePredicate = $this->createNegatePredicate();
        assertFalse($negatePredicate('foo'));
    }
}
