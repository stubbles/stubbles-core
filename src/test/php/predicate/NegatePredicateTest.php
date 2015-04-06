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
 * Test for stubbles\predicate\NegatePredicate.
 *
 * @group  predicate
 * @since  4.0.0
 */
class NegatePredicateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function negatesWrappedPredicate()
    {
        $negatePredicate = new NegatePredicate(function($value) { return 'foo' === $value; });
        assertTrue($negatePredicate('bar'));
    }
}
