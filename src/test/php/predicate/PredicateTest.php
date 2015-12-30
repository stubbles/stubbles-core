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
use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
/**
 * Helper class for the test.
 */
class FooPredicate extends Predicate
{
    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function test($value)
    {
        return 'foo' === $value;
    }
}
/**
 * Test for stubbles\predicate\Predicate.
 *
 * @group  predicate
 * @since  4.0.0
 */
class PredicateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function castFromWithPredicateReturnsInstance()
    {
        $predicate = new FooPredicate();
        assert(Predicate::castFrom($predicate), isSameAs($predicate));
    }

    /**
     * @test
     */
    public function castFromWithCallableReturnsCallablePredicate()
    {
        assert(
                Predicate::castFrom(function($value) { return 'foo' === $value; }),
                isInstanceOf(CallablePredicate::class)
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function castFromWithOtherValueThrowsIllegalArgumentException()
    {
        Predicate::castFrom(new \stdClass());
    }

    /**
     * @test
     */
    public function predicateIsCallable()
    {
        $predicate = new FooPredicate();
        assertTrue($predicate('foo'));
    }

    /**
     * @test
     */
    public function asWellAsReturnsAndPredicate()
    {
        $predicate = new FooPredicate();
        assert(
                $predicate->asWellAs(function($value) { return 'foo' === $value; }),
                isInstanceOf(AndPredicate::class)
        );
    }

    /**
     * @test
     */
    public function orElseReturnsOrPredicate()
    {
        $predicate = new FooPredicate();
        assert(
                $predicate->orElse(function($value) { return 'foo' === $value; }),
                isInstanceOf(OrPredicate::class)
        );
    }

    /**
     * @test
     */
    public function negateReturnsNegatePredicate()
    {
        $predicate = new FooPredicate();
        assert($predicate->negate(), isInstanceOf(NegatePredicate::class));
    }
}
