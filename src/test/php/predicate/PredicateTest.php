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
        assertSame($predicate, Predicate::castFrom($predicate));
    }

    /**
     * @test
     */
    public function castFromWithCallableReturnsCallablePredicate()
    {
        assertInstanceOf(
                'stubbles\predicate\CallablePredicate',
                Predicate::castFrom(function($value) { return 'foo' === $value; })
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
        assertInstanceOf(
                'stubbles\predicate\AndPredicate',
                $predicate->asWellAs(function($value) { return 'foo' === $value; })
        );
    }

    /**
     * @test
     */
    public function orElseReturnsOrPredicate()
    {
        $predicate = new FooPredicate();
        assertInstanceOf(
                'stubbles\predicate\OrPredicate',
                $predicate->orElse(function($value) { return 'foo' === $value; })
        );
    }

    /**
     * @test
     */
    public function negateReturnsNegatePredicate()
    {
        $predicate = new FooPredicate();
        assertInstanceOf(
                'stubbles\predicate\NegatePredicate',
                $predicate->negate()
        );
    }
}
