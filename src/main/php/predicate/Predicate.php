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
use stubbles\lang\exception\IllegalArgumentException;
/**
 * Evaluates if a given value fulfills a criteria.
 *
 * @api
 * @since  4.0.0
 */
abstract class Predicate
{
    /**
     * casts given predicate to a predicate instance
     *
     * @param   \stubbles\predicate\Predicate|callable  $predicate
     * @return  \stubbles\predicate\Predicate
     * @throws  IllegalArgumentException
     */
    public static function castFrom($predicate)
    {
        if ($predicate instanceof self) {
            return $predicate;
        } elseif (is_callable($predicate)) {
            return new CallablePredicate($predicate);
        }

        throw new IllegalArgumentException('Given predicate is neither a callable nor an instance of ' . __CLASS__);
    }

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public abstract function test($value);

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function __invoke($value)
    {
        return $this->test($value);
    }

    /**
     * combines this with another predicate to a predicate which requires both to be true
     *
     * @param   \stubbles\predicate\Predicate|callable  $other
     * @return  \stubbles\predicate\Predicate
     */
    public function asWellAs($other)
    {
        return new AndPredicate($this, self::castFrom($other));
    }

    /**
     * combines this with another predicate to a predicate which requires on of them to be true
     *
     * @param   \stubbles\predicate\Predicate|callable  $other
     * @return  \stubbles\predicate\Predicate
     */
    public function orElse($other)
    {
        return new OrPredicate($this, self::castFrom($other));
    }

    /**
     * returns a negated version of this predicate
     *
     * @return  \stubbles\predicate\Predicate
     */
    public function negate()
    {
        return new NegatePredicate($this);
    }
}
